<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityBooking;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ActivityBookingController extends Controller
{
    /**
     * Display a listing of activity bookings (Admin)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Get admin's activity IDs
            $adminActivityIds = Activity::where('user_id', $user->id)->pluck('id');
            
            // Get activity bookings only for admin's activities
            $bookings = ActivityBooking::with(['activity', 'guest'])
                ->whereIn('activity_id', $adminActivityIds)
                ->orderBy('booking_date', 'desc')
                ->orderBy('booking_time', 'desc')
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'booking_reference' => $booking->booking_reference,
                        'activity' => [
                            'id' => $booking->activity->id,
                            'name' => $booking->activity->name,
                            'location' => $booking->activity->location,
                        ],
                        'guest' => [
                            'id' => $booking->guest->id,
                            'name' => $booking->guest->first_name . ' ' . $booking->guest->last_name,
                            'email' => $booking->guest->email,
                            'phone' => $booking->guest->phone,
                        ],
                        'booking_date' => $booking->booking_date->format('Y-m-d'),
                        'booking_time' => $booking->booking_time->format('H:i'),
                        'participants' => $booking->participants,
                        'total_amount' => $booking->total_amount,
                        'per_person_price' => $booking->per_person_price,
                        'status' => $booking->status,
                        'status_color' => $booking->status_color,
                        'special_requests' => $booking->special_requests,
                        'can_be_cancelled' => $booking->canBeCancelled(),
                        'created_at' => $booking->created_at,
                        'updated_at' => $booking->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activity bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created activity booking
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'activity_id' => 'required|exists:activities,id',
                'guest_id' => 'required|exists:guests,id',
                'booking_date' => 'required|date|after_or_equal:today',
                'booking_time' => 'required|date_format:H:i',
                'participants' => 'required|integer|min:1',
                'special_requests' => 'nullable|string|max:1000',
                'participant_details' => 'nullable|array',
            ]);

            $activity = Activity::findOrFail($validated['activity_id']);
            
            // Check if activity is available on the requested date
            if (!$activity->isAvailableOnDate($validated['booking_date'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Activity is not available on the selected date.'
                ], 400);
            }

            // Check if there are enough slots available
            $availableSlots = $activity->getAvailableSlotsForDate($validated['booking_date']);
            if ($validated['participants'] > $availableSlots) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$availableSlots} slots available for this date."
                ], 400);
            }

            // Check participant limits
            if ($validated['participants'] < $activity->min_participants) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimum {$activity->min_participants} participants required."
                ], 400);
            }

            if ($validated['participants'] > $activity->max_participants) {
                return response()->json([
                    'success' => false,
                    'message' => "Maximum {$activity->max_participants} participants allowed."
                ], 400);
            }

            // Calculate pricing
            $perPersonPrice = $activity->price;
            $totalAmount = $perPersonPrice * $validated['participants'];

            $booking = ActivityBooking::create([
                'activity_id' => $validated['activity_id'],
                'guest_id' => $validated['guest_id'],
                'booking_date' => $validated['booking_date'],
                'booking_time' => $validated['booking_time'],
                'participants' => $validated['participants'],
                'total_amount' => $totalAmount,
                'per_person_price' => $perPersonPrice,
                'special_requests' => $validated['special_requests'],
                'participant_details' => $validated['participant_details'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Activity booking created successfully',
                'data' => [
                    'booking' => $booking->load(['activity', 'guest']),
                    'booking_reference' => $booking->booking_reference
                ]
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
                'message' => 'Failed to create activity booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified activity booking
     */
    public function show(ActivityBooking $activityBooking): JsonResponse
    {
        try {
            $booking = $activityBooking->load(['activity', 'guest']);
            
            return response()->json([
                'success' => true,
                'data' => $booking
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activity booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified activity booking
     */
    public function update(Request $request, ActivityBooking $activityBooking): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
                'admin_notes' => 'nullable|string',
                'cancellation_reason' => 'nullable|string',
                'refund_amount' => 'nullable|numeric|min:0',
            ]);

            if (isset($validated['status'])) {
                switch ($validated['status']) {
                    case 'confirmed':
                        $activityBooking->confirm();
                        break;
                    case 'cancelled':
                        $activityBooking->cancel(
                            $validated['cancellation_reason'] ?? null,
                            $validated['refund_amount'] ?? null
                        );
                        break;
                    case 'completed':
                        $activityBooking->complete();
                        break;
                    default:
                        $activityBooking->update(['status' => $validated['status']]);
                }
            }

            if (isset($validated['admin_notes'])) {
                $activityBooking->update(['admin_notes' => $validated['admin_notes']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Activity booking updated successfully',
                'data' => $activityBooking->fresh()
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
                'message' => 'Failed to update activity booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified activity booking
     */
    public function destroy(ActivityBooking $activityBooking): JsonResponse
    {
        try {
            if (in_array($activityBooking->status, ['confirmed', 'completed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete confirmed or completed bookings. Please cancel first.'
                ], 400);
            }

            $activityBooking->delete();

            return response()->json([
                'success' => true,
                'message' => 'Activity booking deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete activity booking: ' . $e->getMessage()
            ], 500);
        }
    }
}
