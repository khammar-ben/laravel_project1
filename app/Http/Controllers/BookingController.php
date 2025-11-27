<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Offer;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Get admin's room IDs
            $adminRoomIds = Room::where('user_id', $user->id)->pluck('id');
            
            // Get bookings only for admin's rooms
            $bookings = Booking::with(['guest', 'room', 'offer'])
                ->whereIn('room_id', $adminRoomIds)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the user's bookings.
     */
    public function myBookings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $bookings = Booking::with(['guest', 'room', 'offer'])
                ->whereHas('guest', function($query) use ($user) {
                    $query->where('email', $user->email);
                })
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of public bookings (for visitors).
     */
    public function publicIndex(): JsonResponse
    {
        try {
            $bookings = Booking::with(['guest', 'room', 'offer'])
                ->where('status', '!=', 'cancelled')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                // Guest information
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'nationality' => 'nullable|string|max:100',
                'date_of_birth' => 'nullable|date',
                'id_type' => 'nullable|string|max:50',
                'id_number' => 'nullable|string|max:100',
                'address' => 'nullable|string',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:20',
                
                // Booking information
                'room_id' => 'required|exists:rooms,id',
                'offer_id' => 'nullable|exists:offers,id',
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'number_of_guests' => 'required|integer|min:1',
                'special_requests' => 'nullable|string',
            ]);

            // Check room availability
            $room = Room::findOrFail($validated['room_id']);
            if (!$room->isAvailableForBooking()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room is not available for booking'
                ], 400);
            }

            // Check if room has enough capacity
            if ($validated['number_of_guests'] > $room->capacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Number of guests exceeds room capacity'
                ], 400);
            }

            // Check if room can accommodate additional guests
            if (!$room->canAccommodate($validated['number_of_guests'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room does not have enough available beds for the requested number of guests'
                ], 400);
            }

            // Calculate total amount
            $numberOfNights = \Carbon\Carbon::parse($validated['check_in_date'])
                ->diffInDays(\Carbon\Carbon::parse($validated['check_out_date']));
            $originalAmount = $numberOfNights * $room->price;
            $totalAmount = $originalAmount;
            $discountAmount = 0;
            $offer = null;

            // Apply offer discount if provided
            if (!empty($validated['offer_id'])) {
                $offer = Offer::find($validated['offer_id']);
                if ($offer && $offer->isValid()) {
                    // Check if offer requirements are met
                    $meetsGuestRequirement = $validated['number_of_guests'] >= $offer->min_guests;
                    $meetsNightRequirement = !$offer->min_nights || $numberOfNights >= $offer->min_nights;
                    
                    if ($meetsGuestRequirement && $meetsNightRequirement) {
                        $discountAmount = $this->calculateDiscount($originalAmount, $numberOfNights, $offer);
                        $totalAmount = $originalAmount - $discountAmount;
                        
                        // Increment offer usage count
                        $offer->increment('used_count');
                    }
                }
            }

            // Create guest
            $guest = Guest::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'nationality' => $validated['nationality'],
                'date_of_birth' => $validated['date_of_birth'],
                'id_type' => $validated['id_type'],
                'id_number' => $validated['id_number'],
                'address' => $validated['address'],
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_phone' => $validated['emergency_contact_phone'],
            ]);

            // Create booking
            $booking = Booking::create([
                'booking_reference' => 'BK' . strtoupper(Str::random(8)),
                'guest_id' => $guest->id,
                'room_id' => $validated['room_id'],
                'offer_id' => $validated['offer_id'] ?? null,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'number_of_guests' => $validated['number_of_guests'],
                'total_amount' => $totalAmount,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'status' => 'pending',
                'special_requests' => $validated['special_requests'],
            ]);

            // Update room occupancy and status when booking is created
            $room->increment('occupied', $validated['number_of_guests']);
            $room->fresh()->updateStatusBasedOnOccupancy();

            // Send Telegram notification for new booking using room owner's (admin's) Telegram settings
            try {
                $room = $booking->room;
                if ($room && $room->user) {
                    $admin = $room->user; // This is the Admin model
                    if ($admin->telegram_enabled && $admin->telegram_bot_token && $admin->telegram_chat_id) {
                        $telegramService = new TelegramService($admin);
                        $telegramService->sendBookingNotification($booking->load(['guest', 'room']));
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't fail the booking creation
                \Log::error('Failed to send Telegram notification: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking request submitted successfully',
                'data' => [
                    'booking' => $booking->load(['guest', 'room', 'offer']),
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
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource (only admin's bookings).
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $user = $request->user();
            $adminRoomIds = Room::where('user_id', $user->id)->pluck('id');
            
            $booking = Booking::with(['guest', 'room', 'offer'])
                ->whereIn('room_id', $adminRoomIds)
                ->findOrFail($id);
                
            return response()->json([
                'success' => true,
                'data' => $booking
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or you do not have permission to view it'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = $request->user();
            $adminRoomIds = Room::where('user_id', $user->id)->pluck('id');
            
            $booking = Booking::whereIn('room_id', $adminRoomIds)->findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            ]);

            $updateData = ['status' => $validated['status']];

            // Set timestamps and update room status based on booking status
            if ($validated['status'] === 'confirmed' && $booking->status === 'pending') {
                $updateData['confirmed_at'] = now();
                // Room status already updated when booking was created
                $booking->room->fresh()->updateStatusBasedOnOccupancy();
            } elseif ($validated['status'] === 'checked_in' && $booking->status === 'confirmed') {
                $updateData['checked_in_at'] = now();
                // Occupancy already counted when booking was created
                $booking->room->fresh()->updateStatusBasedOnOccupancy();
            } elseif ($validated['status'] === 'checked_out' && $booking->status === 'checked_in') {
                $updateData['checked_out_at'] = now();
                // Decrease occupancy when guest checks out
                $booking->room->decrement('occupied', $booking->number_of_guests);
                // Update room status based on new occupancy
                $booking->room->fresh()->updateStatusBasedOnOccupancy();
            } elseif ($validated['status'] === 'cancelled') {
                // If booking is cancelled, decrease occupancy and update status
                $booking->room->decrement('occupied', $booking->number_of_guests);
                $booking->room->fresh()->updateStatusBasedOnOccupancy();
            }

            $booking->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully',
                'data' => $booking->load(['guest', 'room', 'offer'])
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
                'message' => 'Failed to update booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (only admin's bookings).
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $user = $request->user();
            $adminRoomIds = Room::where('user_id', $user->id)->pluck('id');
            
            $booking = Booking::whereIn('room_id', $adminRoomIds)->findOrFail($id);
            $room = $booking->room;
            
            // Decrement room occupancy for any active booking
            if (in_array($booking->status, ['pending', 'confirmed', 'checked_in'])) {
                $room->decrement('occupied', $booking->number_of_guests);
            }
            
            // Delete the booking
            $booking->delete();
            
            // Update room status based on current occupancy
            $room->fresh()->updateStatusBasedOnOccupancy();

            return response()->json([
                'success' => true,
                'message' => 'Booking deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or you do not have permission to delete it'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available rooms for booking
     */
    public function getAvailableRooms(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'number_of_guests' => 'required|integer|min:1',
            ]);

            // Get rooms that meet basic criteria
            $rooms = Room::where('capacity', '>=', $validated['number_of_guests'])
                ->get()
                ->filter(function ($room) {
                    return $room->isAvailableForBooking();
                });

            // Filter out rooms with conflicting bookings
            $availableRooms = $rooms->filter(function ($room) use ($validated) {
                $conflictingBookings = Booking::where('room_id', $room->id)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($query) use ($validated) {
                        $query->whereBetween('check_in_date', [$validated['check_in_date'], $validated['check_out_date']])
                              ->orWhereBetween('check_out_date', [$validated['check_in_date'], $validated['check_out_date']])
                              ->orWhere(function ($q) use ($validated) {
                                  $q->where('check_in_date', '<=', $validated['check_in_date'])
                                    ->where('check_out_date', '>=', $validated['check_out_date']);
                              });
                    })
                    ->exists();

                return !$conflictingBookings;
            });

            return response()->json([
                'success' => true,
                'data' => $availableRooms->values()
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
                'message' => 'Failed to fetch available rooms: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate discount amount based on offer type
     */
    private function calculateDiscount($originalAmount, $numberOfNights, Offer $offer): float
    {
        switch ($offer->discount_type) {
            case 'percentage':
                return $originalAmount * ($offer->discount_value / 100);
            case 'fixed_amount':
                return min($offer->discount_value, $originalAmount);
            case 'free_night':
                $roomPrice = $originalAmount / $numberOfNights;
                $freeNights = min($offer->discount_value, $numberOfNights);
                return $roomPrice * $freeNights;
            default:
                return 0;
        }
    }
}
