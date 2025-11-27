<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RoomAvailabilityService
{
    /**
     * Check if a room is available for specific dates and guest count
     */
    public function isRoomAvailable(int $roomId, string $checkInDate, string $checkOutDate, int $numberOfGuests): bool
    {
        $room = Room::find($roomId);
        
        if (!$room) {
            return false;
        }

        // Check basic room availability
        if (!$room->isAvailableForBooking()) {
            return false;
        }

        // Check capacity
        if ($numberOfGuests > $room->capacity) {
            return false;
        }

        // Check if room can accommodate additional guests
        if (!$room->canAccommodate($numberOfGuests)) {
            return false;
        }

        // Check for date conflicts
        if ($this->hasDateConflicts($roomId, $checkInDate, $checkOutDate)) {
            return false;
        }

        return true;
    }

    /**
     * Get all available rooms for specific dates and guest count
     */
    public function getAvailableRooms(string $checkInDate, string $checkOutDate, int $numberOfGuests, ?string $roomType = null): Collection
    {
        $query = Room::where('capacity', '>=', $numberOfGuests)
            ->where('status', '!=', 'maintenance');

        if ($roomType && $roomType !== 'all') {
            $query->where('type', $roomType);
        }

        $rooms = $query->get();

        return $rooms->filter(function ($room) use ($checkInDate, $checkOutDate, $numberOfGuests) {
            return $this->isRoomAvailable($room->id, $checkInDate, $checkOutDate, $numberOfGuests);
        });
    }

    /**
     * Search available rooms with detailed filtering
     */
    public function searchAvailableRooms(array $searchParams): array
    {
        $checkInDate = $searchParams['check_in_date'];
        $checkOutDate = $searchParams['check_out_date'];
        $numberOfGuests = $searchParams['number_of_guests'];
        $roomType = $searchParams['room_type'] ?? null;
        $maxPrice = $searchParams['max_price'] ?? null;
        $minPrice = $searchParams['min_price'] ?? null;

        $query = Room::where('capacity', '>=', $numberOfGuests)
            ->where('status', '!=', 'maintenance');

        // Apply filters
        if ($roomType && $roomType !== 'all') {
            $query->where('type', $roomType);
        }

        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }

        $rooms = $query->get();

        $availableRooms = $rooms->filter(function ($room) use ($checkInDate, $checkOutDate, $numberOfGuests) {
            return $this->isRoomAvailable($room->id, $checkInDate, $checkOutDate, $numberOfGuests);
        });

        // Calculate total nights for pricing
        $totalNights = \Carbon\Carbon::parse($checkInDate)->diffInDays(\Carbon\Carbon::parse($checkOutDate));

        return $availableRooms->map(function ($room) use ($totalNights) {
            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'name' => $room->name,
                'type' => $room->type,
                'capacity' => $room->capacity,
                'floor' => $room->floor,
                'price' => $room->price,
                'status' => $room->status,
                'description' => $room->description,
                'image_url' => $room->image_url,
                'amenities' => $room->amenities,
                'available_beds' => $room->capacity - $room->occupied,
                'total_price' => $room->price * $totalNights,
                'price_per_night' => $room->price,
                'is_available' => true,
                'can_accommodate' => $room->canAccommodate($room->capacity - $room->occupied)
            ];
        })->values()->toArray();
    }

    /**
     * Check for date conflicts with existing bookings
     */
    public function hasDateConflicts(int $roomId, string $checkInDate, string $checkOutDate): bool
    {
        $conflictingBookings = Booking::where('room_id', $roomId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                      ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                      ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                          $q->where('check_in_date', '<=', $checkInDate)
                            ->where('check_out_date', '>=', $checkOutDate);
                      });
            })
            ->exists();

        return $conflictingBookings;
    }

    /**
     * Get room availability calendar for a specific room
     */
    public function getRoomAvailabilityCalendar(int $roomId, int $months = 3): array
    {
        $room = Room::find($roomId);
        if (!$room) {
            return [];
        }

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonths($months)->endOfMonth();
        
        $bookings = Booking::where('room_id', $roomId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('check_in_date', [$startDate, $endDate])
            ->orWhereBetween('check_out_date', [$startDate, $endDate])
            ->get();

        $availability = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $isAvailable = true;
            $conflictingBookings = [];

            foreach ($bookings as $booking) {
                if ($currentDate->between($booking->check_in_date, $booking->check_out_date->subDay())) {
                    $isAvailable = false;
                    $conflictingBookings[] = [
                        'booking_reference' => $booking->booking_reference,
                        'guest_name' => $booking->guest->first_name . ' ' . $booking->guest->last_name,
                        'status' => $booking->status,
                        'number_of_guests' => $booking->number_of_guests
                    ];
                }
            }

            $availability[$dateStr] = [
                'date' => $dateStr,
                'is_available' => $isAvailable,
                'room_status' => $room->status,
                'available_spaces' => $room->getAvailableSpaces(),
                'conflicting_bookings' => $conflictingBookings
            ];

            $currentDate->addDay();
        }

        return $availability;
    }

    /**
     * Get room occupancy summary
     */
    public function getRoomOccupancySummary(): array
    {
        $rooms = Room::with(['bookings' => function ($query) {
            $query->whereIn('status', ['confirmed', 'checked_in']);
        }])->get();

        $summary = [
            'total_rooms' => $rooms->count(),
            'total_capacity' => $rooms->sum('capacity'),
            'total_occupied' => $rooms->sum('occupied'),
            'total_available' => $rooms->sum(function ($room) {
                return $room->getAvailableSpaces();
            }),
            'occupancy_rate' => 0,
            'rooms_by_status' => [
                'available' => 0,
                'occupied' => 0,
                'full' => 0,
                'maintenance' => 0
            ],
            'rooms_by_type' => []
        ];

        foreach ($rooms as $room) {
            // Count by status
            $summary['rooms_by_status'][$room->status]++;

            // Count by type
            if (!isset($summary['rooms_by_type'][$room->type])) {
                $summary['rooms_by_type'][$room->type] = [
                    'total' => 0,
                    'occupied' => 0,
                    'available' => 0
                ];
            }
            $summary['rooms_by_type'][$room->type]['total']++;
            $summary['rooms_by_type'][$room->type]['occupied'] += $room->occupied;
            $summary['rooms_by_type'][$room->type]['available'] += $room->getAvailableSpaces();
        }

        // Calculate occupancy rate
        if ($summary['total_capacity'] > 0) {
            $summary['occupancy_rate'] = round(($summary['total_occupied'] / $summary['total_capacity']) * 100, 2);
        }

        return $summary;
    }

    /**
     * Update room status based on current bookings
     */
    public function updateRoomStatus(int $roomId): bool
    {
        $room = Room::find($roomId);
        if (!$room) {
            return false;
        }

        // Sync occupancy with actual bookings
        $room->syncOccupancy();
        
        return true;
    }

    /**
     * Update all room statuses
     */
    public function updateAllRoomStatuses(): int
    {
        $updatedCount = 0;
        $rooms = Room::all();

        foreach ($rooms as $room) {
            if ($this->updateRoomStatus($room->id)) {
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * Get rooms that need maintenance or cleaning
     */
    public function getRoomsNeedingAttention(): Collection
    {
        $rooms = Room::where(function ($query) {
            $query->where('status', 'maintenance')
                  ->orWhere('last_cleaned', '<', Carbon::now()->subDays(7))
                  ->orWhereNull('last_cleaned');
        })->get();

        return $rooms->map(function ($room) {
            $daysSinceCleaned = $room->last_cleaned ? 
                Carbon::now()->diffInDays($room->last_cleaned) : null;

            return [
                'room' => $room,
                'needs_cleaning' => $daysSinceCleaned === null || $daysSinceCleaned >= 7,
                'days_since_cleaned' => $daysSinceCleaned,
                'needs_maintenance' => $room->status === 'maintenance'
            ];
        });
    }

    /**
     * Get upcoming check-ins and check-outs
     */
    public function getUpcomingTransitions(int $days = 7): array
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays($days);

        $checkIns = Booking::whereBetween('check_in_date', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'pending'])
            ->with(['guest', 'room'])
            ->get();

        $checkOuts = Booking::whereBetween('check_out_date', [$startDate, $endDate])
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->with(['guest', 'room'])
            ->get();

        return [
            'check_ins' => $checkIns,
            'check_outs' => $checkOuts,
            'total_check_ins' => $checkIns->count(),
            'total_check_outs' => $checkOuts->count()
        ];
    }

    /**
     * Get room utilization report
     */
    public function getRoomUtilizationReport(string $startDate, string $endDate): array
    {
        $rooms = Room::with(['bookings' => function ($query) use ($startDate, $endDate) {
            $query->where('status', '!=', 'cancelled')
                  ->where(function ($q) use ($startDate, $endDate) {
                      $q->whereBetween('check_in_date', [$startDate, $endDate])
                        ->orWhereBetween('check_out_date', [$startDate, $endDate])
                        ->orWhere(function ($subQ) use ($startDate, $endDate) {
                            $subQ->where('check_in_date', '<=', $startDate)
                                 ->where('check_out_date', '>=', $endDate);
                        });
                  });
        }])->get();

        $report = [];
        foreach ($rooms as $room) {
            $totalNights = 0;
            $occupiedNights = 0;

            $currentDate = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);

            while ($currentDate->lte($endDateCarbon)) {
                $totalNights++;
                $isOccupied = false;

                foreach ($room->bookings as $booking) {
                    if ($currentDate->between($booking->check_in_date, $booking->check_out_date->subDay())) {
                        $isOccupied = true;
                        break;
                    }
                }

                if ($isOccupied) {
                    $occupiedNights++;
                }

                $currentDate->addDay();
            }

            $utilizationRate = $totalNights > 0 ? round(($occupiedNights / $totalNights) * 100, 2) : 0;

            $report[] = [
                'room' => $room,
                'total_nights' => $totalNights,
                'occupied_nights' => $occupiedNights,
                'utilization_rate' => $utilizationRate,
                'revenue' => $room->bookings->sum('total_amount')
            ];
        }

        return $report;
    }
}
