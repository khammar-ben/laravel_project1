<?php

namespace App\Http\Controllers;

use App\Services\RoomAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoomAvailabilityController extends Controller
{
    protected RoomAvailabilityService $availabilityService;

    public function __construct(RoomAvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * Get available rooms for specific dates and guest count
     */
    public function getAvailableRooms(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'number_of_guests' => 'required|integer|min:1',
                'room_type' => 'nullable|string',
            ]);

            $rooms = $this->availabilityService->getAvailableRooms(
                $validated['check_in_date'],
                $validated['check_out_date'],
                $validated['number_of_guests'],
                $validated['room_type'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $rooms->values(),
                'count' => $rooms->count()
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
                'message' => 'Failed to get available rooms: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if a specific room is available
     */
    public function checkRoomAvailability(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'number_of_guests' => 'required|integer|min:1',
            ]);

            $isAvailable = $this->availabilityService->isRoomAvailable(
                $validated['room_id'],
                $validated['check_in_date'],
                $validated['check_out_date'],
                $validated['number_of_guests']
            );

            return response()->json([
                'success' => true,
                'available' => $isAvailable,
                'message' => $isAvailable ? 'Room is available' : 'Room is not available'
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
                'message' => 'Failed to check room availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room availability calendar
     */
    public function getRoomAvailabilityCalendar(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'months' => 'nullable|integer|min:1|max:12',
            ]);

            $calendar = $this->availabilityService->getRoomAvailabilityCalendar(
                $validated['room_id'],
                $validated['months'] ?? 3
            );

            return response()->json([
                'success' => true,
                'data' => $calendar
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
                'message' => 'Failed to get room availability calendar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room occupancy summary
     */
    public function getOccupancySummary(): JsonResponse
    {
        try {
            $summary = $this->availabilityService->getRoomOccupancySummary();

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get occupancy summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update room status
     */
    public function updateRoomStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
            ]);

            $updated = $this->availabilityService->updateRoomStatus($validated['room_id']);

            return response()->json([
                'success' => $updated,
                'message' => $updated ? 'Room status updated successfully' : 'Failed to update room status'
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
                'message' => 'Failed to update room status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update all room statuses
     */
    public function updateAllRoomStatuses(): JsonResponse
    {
        try {
            $updatedCount = $this->availabilityService->updateAllRoomStatuses();

            return response()->json([
                'success' => true,
                'message' => "Updated {$updatedCount} room statuses",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update room statuses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get rooms needing attention (maintenance/cleaning)
     */
    public function getRoomsNeedingAttention(): JsonResponse
    {
        try {
            $rooms = $this->availabilityService->getRoomsNeedingAttention();

            return response()->json([
                'success' => true,
                'data' => $rooms,
                'count' => $rooms->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get rooms needing attention: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming check-ins and check-outs
     */
    public function getUpcomingTransitions(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'days' => 'nullable|integer|min:1|max:30',
            ]);

            $transitions = $this->availabilityService->getUpcomingTransitions(
                $validated['days'] ?? 7
            );

            return response()->json([
                'success' => true,
                'data' => $transitions
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
                'message' => 'Failed to get upcoming transitions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room utilization report
     */
    public function getRoomUtilizationReport(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);

            $report = $this->availabilityService->getRoomUtilizationReport(
                $validated['start_date'],
                $validated['end_date']
            );

            return response()->json([
                'success' => true,
                'data' => $report
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
                'message' => 'Failed to get utilization report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search available rooms with advanced filtering
     */
    public function searchRooms(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'number_of_guests' => 'required|integer|min:1',
                'room_type' => 'nullable|string',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
            ]);

            $rooms = $this->availabilityService->searchAvailableRooms($validated);

            return response()->json([
                'success' => true,
                'data' => $rooms,
                'count' => count($rooms),
                'search_params' => $validated
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
                'message' => 'Failed to search rooms: ' . $e->getMessage()
            ], 500);
        }
    }
}
