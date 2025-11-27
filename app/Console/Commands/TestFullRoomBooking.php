<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\Booking;
use App\Models\Guest;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestFullRoomBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:full-room-booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test full room booking scenarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Testing full room booking scenarios...");
        
        // Test with room R004 (Private Double - capacity 2)
        $room = Room::where('room_number', 'R004')->first();
        
        if (!$room) {
            $this->error("Room R004 not found.");
            return;
        }
        
        $this->info("Testing with room: {$room->room_number} ({$room->name})");
        $this->info("Room capacity: {$room->capacity}");
        $this->info("Initial status: {$room->status}");
        $this->info("Initial occupancy: {$room->occupied}/{$room->capacity}");
        
        // Test 1: Book 1 guest (should be occupied)
        $this->info("\n--- Test 1: Booking 1 guest ---");
        $this->createTestBooking($room, 1);
        $this->displayRoomStatus($room);
        
        // Test 2: Try to book another guest (should work, room becomes full)
        $this->info("\n--- Test 2: Booking another guest (room should become full) ---");
        $this->createTestBooking($room, 1);
        $this->displayRoomStatus($room);
        
        // Test 3: Try to book when room is full (should fail)
        $this->info("\n--- Test 3: Trying to book when room is full ---");
        $this->testFullRoomBooking($room);
        
        // Test 4: Check out one guest (room should become occupied)
        $this->info("\n--- Test 4: Checking out one guest ---");
        $booking = $room->bookings()->where('status', 'pending')->first();
        if ($booking) {
            $booking->update(['status' => 'checked_out']);
            $room->decrement('occupied', $booking->number_of_guests);
            $room->fresh()->updateStatusBasedOnOccupancy();
        }
        $this->displayRoomStatus($room);
        
        // Test 5: Check out remaining guest (room should become available)
        $this->info("\n--- Test 5: Checking out remaining guest ---");
        $booking = $room->bookings()->where('status', 'pending')->first();
        if ($booking) {
            $booking->update(['status' => 'checked_out']);
            $room->decrement('occupied', $booking->number_of_guests);
            $room->fresh()->updateStatusBasedOnOccupancy();
        }
        $this->displayRoomStatus($room);
        
        // Clean up test bookings
        $this->info("\n--- Cleaning up test bookings ---");
        $room->bookings()->where('status', 'checked_out')->delete();
        
        $this->info("\n✅ Full room booking test completed!");
    }
    
    private function createTestBooking(Room $room, int $guests)
    {
        // Create test guest
        $guest = Guest::create([
            'first_name' => 'Test',
            'last_name' => 'Guest',
            'email' => 'test' . Str::random(5) . '@example.com',
        ]);
        
        // Create booking
        $booking = Booking::create([
            'booking_reference' => 'BK' . strtoupper(Str::random(8)),
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'check_in_date' => now()->addDay(),
            'check_out_date' => now()->addDays(2),
            'number_of_guests' => $guests,
            'total_amount' => 100.00,
            'status' => 'pending',
        ]);
        
        // Update room occupancy
        $room->increment('occupied', $guests);
        $room->fresh()->updateStatusBasedOnOccupancy();
        
        $this->info("Created booking for {$guests} guest(s)");
    }
    
    private function testFullRoomBooking(Room $room)
    {
        if ($room->canAccommodate(1)) {
            $this->info("❌ Room should be full but can still accommodate guests!");
        } else {
            $this->info("✅ Room correctly identified as full - cannot accommodate more guests");
        }
    }
    
    private function displayRoomStatus(Room $room)
    {
        $room = $room->fresh();
        $this->info("Room status: {$room->status}");
        $this->info("Room occupancy: {$room->occupied}/{$room->capacity}");
        $this->info("Available spaces: {$room->getAvailableSpaces()}");
        $this->info("Can accommodate 1 more: " . ($room->canAccommodate(1) ? 'Yes' : 'No'));
    }
}