<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Console\Command;

class TestRoomStatusLogic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:room-status-logic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test room status update logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Testing room status update logic...");
        
        // Test with room R004 (available room)
        $room = Room::where('room_number', 'R004')->first();
        
        if (!$room) {
            $this->error("Room R004 not found.");
            return;
        }
        
        $this->info("Testing with room: {$room->room_number} ({$room->name})");
        $this->info("Initial status: {$room->status}");
        $this->info("Initial occupancy: {$room->occupied}/{$room->capacity}");
        
        // Test 1: Create a booking (should set room to occupied)
        $this->info("\n--- Test 1: Creating booking ---");
        $room->update(['status' => 'occupied']);
        $this->info("Room status after booking creation: {$room->fresh()->status}");
        
        // Test 2: Confirm booking (should stay occupied)
        $this->info("\n--- Test 2: Confirming booking ---");
        $this->info("Room status after confirmation: {$room->fresh()->status}");
        
        // Test 3: Check in (should stay occupied, increment occupancy)
        $this->info("\n--- Test 3: Checking in ---");
        $room->increment('occupied', 1);
        $room->update(['status' => 'occupied']);
        $this->info("Room status after check-in: {$room->fresh()->status}");
        $this->info("Room occupancy after check-in: {$room->fresh()->occupied}");
        
        // Test 4: Check out (should update status based on occupancy)
        $this->info("\n--- Test 4: Checking out ---");
        $room->decrement('occupied', 1);
        $room->fresh()->updateStatusBasedOnOccupancy();
        $this->info("Room status after check-out: {$room->fresh()->status}");
        $this->info("Room occupancy after check-out: {$room->fresh()->occupied}");
        
        // Test 5: Fill room to capacity
        $this->info("\n--- Test 5: Filling room to capacity ---");
        $room->update(['occupied' => $room->capacity]);
        $room->updateStatusBasedOnOccupancy();
        $this->info("Room status when full: {$room->fresh()->status}");
        
        // Test 6: Empty room
        $this->info("\n--- Test 6: Emptying room ---");
        $room->update(['occupied' => 0]);
        $room->updateStatusBasedOnOccupancy();
        $this->info("Room status when empty: {$room->fresh()->status}");
        
        $this->info("\n✅ Room status logic test completed!");
    }
}
