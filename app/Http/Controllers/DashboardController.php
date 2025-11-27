<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Activity;
use App\Models\ActivityBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics for the authenticated admin
     */
    public function getStats(): JsonResponse
    {
        try {
            $adminId = Auth::id();
            
            // Get admin's rooms only (no fallback to all rooms)
            $adminRooms = Room::where('user_id', $adminId)->get();
            $roomIds = $adminRooms->pluck('id');
            
            // Get admin's activities only (no fallback to all activities)
            $adminActivities = Activity::where('user_id', $adminId)->get();
            $activityIds = $adminActivities->pluck('id');
            
            // Get bookings for admin's rooms
            $adminBookings = Booking::whereIn('room_id', $roomIds)->get();
            
            // Get activity bookings for admin's activities
            $adminActivityBookings = ActivityBooking::whereIn('activity_id', $activityIds)->get();
            
            // Calculate statistics
            $totalBookings = $adminBookings->count();
            $currentGuests = $adminBookings->whereIn('status', ['checked_in', 'confirmed'])->sum('number_of_guests');
            
            $totalCapacity = $adminRooms->sum('capacity');
            $totalOccupied = $adminRooms->sum('occupied');
            $availableBeds = $totalCapacity - $totalOccupied;
            
            // Debug information
            \Log::info('Dashboard Stats Debug', [
                'adminId' => $adminId,
                'roomsCount' => $adminRooms->count(),
                'bookingsCount' => $adminBookings->count(),
                'totalCapacity' => $totalCapacity,
                'totalOccupied' => $totalOccupied,
                'availableBeds' => $availableBeds
            ]);
            
            // Calculate monthly revenue (current month)
            $currentMonth = Carbon::now();
            $monthlyRevenue = $adminBookings
                ->where('status', '!=', 'cancelled')
                ->filter(function ($booking) use ($currentMonth) {
                    $checkInDate = Carbon::parse($booking->check_in_date);
                    return $checkInDate->month === $currentMonth->month && 
                           $checkInDate->year === $currentMonth->year;
                })
                ->sum(function ($booking) {
                    return (float) ($booking->total_amount ?? 0);
                });
            
            // Add activity revenue
            $activityRevenue = $adminActivityBookings
                ->where('status', '!=', 'cancelled')
                ->filter(function ($booking) use ($currentMonth) {
                    $bookingDate = Carbon::parse($booking->booking_date);
                    return $bookingDate->month === $currentMonth->month && 
                           $bookingDate->year === $currentMonth->year;
                })
                ->sum(function ($booking) {
                    return (float) ($booking->total_amount ?? 0);
                });
            
            $monthlyRevenue += $activityRevenue;
            
            // If no monthly revenue, calculate total revenue for all time
            if ($monthlyRevenue == 0) {
                $monthlyRevenue = $adminBookings
                    ->where('status', '!=', 'cancelled')
                    ->sum(function ($booking) {
                        return (float) ($booking->total_amount ?? 0);
                    });
                
                $monthlyRevenue += $adminActivityBookings
                    ->where('status', '!=', 'cancelled')
                    ->sum(function ($booking) {
                        return (float) ($booking->total_amount ?? 0);
                    });
            }
            
            $totalActivities = $adminActivities->count();
            $activeActivities = $adminActivities->where('is_active', true)->count();
            
            // Additional statistics
            $totalRevenue = $adminBookings
                ->where('status', '!=', 'cancelled')
                ->sum(function ($booking) {
                    return (float) ($booking->total_amount ?? 0);
                });
            
            $totalRevenue += $adminActivityBookings
                ->where('status', '!=', 'cancelled')
                ->sum(function ($booking) {
                    return (float) ($booking->total_amount ?? 0);
                });
            
            $occupancyRate = $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 2) : 0;
            
            // Get booking status breakdown
            $bookingStatusBreakdown = $adminBookings->groupBy('status')->map->count();
            
            // Get room type breakdown
            $roomTypeBreakdown = $adminRooms->groupBy('type')->map->count();
            
            // Get recent bookings (last 5)
            $recentBookings = $adminBookings
                ->sortByDesc('created_at')
                ->take(5)
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'booking_reference' => $booking->booking_reference,
                        'guest_name' => $booking->guest->first_name . ' ' . $booking->guest->last_name,
                        'room_name' => $booking->room->name,
                        'check_in_date' => $booking->check_in_date,
                        'check_out_date' => $booking->check_out_date,
                        'status' => $booking->status,
                        'total_amount' => $booking->total_amount,
                        'created_at' => $booking->created_at,
                    ];
                })
                ->values() // Convert collection to array with sequential keys
                ->toArray(); // Ensure it's a proper array
            
            return response()->json([
                'success' => true,
                'data' => [
                    'totalBookings' => $totalBookings,
                    'currentGuests' => $currentGuests,
                    'availableBeds' => $availableBeds,
                    'totalCapacity' => $totalCapacity,
                    'totalOccupied' => $totalOccupied,
                    'occupancyRate' => $occupancyRate,
                    'monthlyRevenue' => round($monthlyRevenue, 2),
                    'totalRevenue' => round($totalRevenue, 2),
                    'totalActivities' => $totalActivities,
                    'activeActivities' => $activeActivities,
                    'bookingStatusBreakdown' => $bookingStatusBreakdown,
                    'roomTypeBreakdown' => $roomTypeBreakdown,
                    'recentBookings' => $recentBookings,
                    'debug' => [
                        'adminId' => $adminId,
                        'roomsCount' => $adminRooms->count(),
                        'bookingsCount' => $adminBookings->count(),
                        'activitiesCount' => $adminActivities->count(),
                    ]
                ],
                'message' => 'Dashboard statistics retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics for public access (testing)
     */
    public function getStatsPublic(): JsonResponse
    {
        try {
            // Get all data for public dashboard overview
            $allRooms = Room::all();
            $allBookings = Booking::all();
            $allActivities = Activity::all();
            $allActivityBookings = ActivityBooking::all();
            
            // Calculate statistics
            $totalBookings = $allBookings->count();
            $currentGuests = $allBookings->whereIn('status', ['checked_in', 'confirmed'])->sum('number_of_guests');
            
            $totalCapacity = $allRooms->sum('capacity');
            $totalOccupied = $allRooms->sum('occupied');
            $availableBeds = $totalCapacity - $totalOccupied;
            
            // Calculate revenue
            $totalRevenue = $allBookings
                ->where('status', '!=', 'cancelled')
                ->sum(function ($booking) {
                    return (float) ($booking->total_amount ?? 0);
                });
            
            $totalRevenue += $allActivityBookings
                ->where('status', '!=', 'cancelled')
                ->sum(function ($booking) {
                    return (float) ($booking->total_amount ?? 0);
                });
            
            $occupancyRate = $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 2) : 0;
            
            // Get booking status breakdown
            $bookingStatusBreakdown = $allBookings->groupBy('status')->map->count();
            
            // Get room type breakdown
            $roomTypeBreakdown = $allRooms->groupBy('type')->map->count();
            
            $totalActivities = $allActivities->count();
            $activeActivities = $allActivities->where('is_active', true)->count();
            
            // Get recent bookings (last 5)
            $recentBookings = $allBookings
                ->sortByDesc('created_at')
                ->take(5)
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'booking_reference' => $booking->booking_reference,
                        'guest_name' => ($booking->guest->first_name ?? '') . ' ' . ($booking->guest->last_name ?? ''),
                        'room_name' => $booking->room->name ?? 'Unknown Room',
                        'check_in_date' => $booking->check_in_date,
                        'check_out_date' => $booking->check_out_date,
                        'status' => $booking->status,
                        'total_amount' => $booking->total_amount,
                        'created_at' => $booking->created_at,
                    ];
                })
                ->values() // Convert collection to array with sequential keys
                ->toArray(); // Ensure it's a proper array
            
            return response()->json([
                'success' => true,
                'data' => [
                    'totalBookings' => $totalBookings,
                    'currentGuests' => $currentGuests,
                    'availableBeds' => $availableBeds,
                    'totalCapacity' => $totalCapacity,
                    'totalOccupied' => $totalOccupied,
                    'occupancyRate' => $occupancyRate,
                    'totalRevenue' => round($totalRevenue, 2),
                    'totalActivities' => $totalActivities,
                    'activeActivities' => $activeActivities,
                    'bookingStatusBreakdown' => $bookingStatusBreakdown,
                    'roomTypeBreakdown' => $roomTypeBreakdown,
                    'recentBookings' => $recentBookings,
                    'debug' => [
                        'roomsCount' => $allRooms->count(),
                        'bookingsCount' => $allBookings->count(),
                        'activitiesCount' => $allActivities->count(),
                    ]
                ],
                'message' => 'Public dashboard statistics retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve public dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
