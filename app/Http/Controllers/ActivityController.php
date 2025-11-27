<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ActivityController extends Controller
{
    /**
     * Display a listing of activities (Admin only - shows only admin's activities)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $activities = Activity::where('user_id', $user->id)
                ->with(['bookings' => function($query) {
                    $query->whereIn('status', ['confirmed', 'pending']);
                }])
                ->orderBy('created_at', 'desc')
                ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'description' => $activity->description,
                    'short_description' => $activity->short_description,
                    'price' => $activity->price,
                    'duration_minutes' => $activity->duration_minutes,
                    'formatted_duration' => $activity->formatted_duration,
                    'max_participants' => $activity->max_participants,
                    'min_participants' => $activity->min_participants,
                    'difficulty_level' => $activity->difficulty_level,
                    'difficulty_color' => $activity->difficulty_color,
                    'is_active' => $activity->is_active,
                    'total_bookings' => $activity->bookings->count(),
                    'confirmed_bookings' => $activity->bookings->where('status', 'confirmed')->count(),
                    'created_at' => $activity->created_at,
                    'updated_at' => $activity->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a public listing of activities (No auth required)
     */
    public function publicIndex(): JsonResponse
    {
        try {
            $activities = Activity::active()
                ->orderBy('rating', 'desc')
                ->orderBy('name')
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'name' => $activity->name,
                        'description' => $activity->description,
                        'short_description' => $activity->short_description,
                        'price' => $activity->price,
                        'duration_minutes' => $activity->duration_minutes,
                        'formatted_duration' => $activity->formatted_duration,
                        'max_participants' => $activity->max_participants,
                        'min_participants' => $activity->min_participants,
                        'difficulty_level' => $activity->difficulty_level,
                        'difficulty_color' => $activity->difficulty_color,
                        'location' => $activity->location,
                        'meeting_point' => $activity->meeting_point,
                        'available_days' => $activity->available_days,
                        'start_time' => $activity->start_time?->format('H:i'),
                        'end_time' => $activity->end_time?->format('H:i'),
                        'image_url' => $activity->image_url,
                        'rating' => $activity->rating,
                        'total_reviews' => $activity->total_reviews,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created activity
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'short_description' => 'nullable|string|max:500',
                'price' => 'required|numeric|min:0',
                'duration_minutes' => 'required|integer|min:1',
                'max_participants' => 'required|integer|min:1',
                'min_participants' => 'nullable|integer|min:1',
                'difficulty_level' => 'required|in:easy,moderate,hard',
                'location' => 'nullable|string|max:255',
                'meeting_point' => 'nullable|string|max:255',
                'available_days' => 'nullable|array',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i',
                'image_url' => 'nullable|url',
                'is_active' => 'boolean',
                'advance_booking_hours' => 'nullable|integer|min:1',
                'what_to_bring' => 'nullable|string',
            ]);

            // Add the authenticated user's ID
            $validated['user_id'] = $request->user()->id;
            $activity = Activity::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Activity created successfully',
                'data' => $activity
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create activity: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified activity
     */
    public function show(Request $request, Activity $activity): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Ensure admin can only view their own activities
            if ($activity->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Activity not found or you do not have permission to view it'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $activity
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activity: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified activity
     */
    public function update(Request $request, Activity $activity): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Ensure admin can only update their own activities
            if ($activity->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Activity not found or you do not have permission to update it'
                ], 404);
            }
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'short_description' => 'nullable|string|max:500',
                'price' => 'sometimes|numeric|min:0',
                'duration_minutes' => 'sometimes|integer|min:1',
                'max_participants' => 'sometimes|integer|min:1',
                'min_participants' => 'nullable|integer|min:1',
                'difficulty_level' => 'sometimes|in:easy,moderate,hard',
                'location' => 'nullable|string|max:255',
                'meeting_point' => 'nullable|string|max:255',
                'available_days' => 'nullable|array',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i',
                'image_url' => 'nullable|url',
                'is_active' => 'boolean',
                'advance_booking_hours' => 'nullable|integer|min:1',
                'what_to_bring' => 'nullable|string',
            ]);

            $activity->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Activity updated successfully',
                'data' => $activity
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update activity: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified activity
     */
    public function destroy(Request $request, Activity $activity): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Ensure admin can only delete their own activities
            if ($activity->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Activity not found or you do not have permission to delete it'
                ], 404);
            }
            
            // Check if there are any confirmed bookings
            $confirmedBookings = $activity->bookings()->whereIn('status', ['confirmed', 'pending'])->count();
            
            if ($confirmedBookings > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete activity with existing bookings. Please cancel all bookings first.'
                ], 400);
            }

            $activity->delete();

            return response()->json([
                'success' => true,
                'message' => 'Activity deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete activity: ' . $e->getMessage()
            ], 500);
        }
    }
}
