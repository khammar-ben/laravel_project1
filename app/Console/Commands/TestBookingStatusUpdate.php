<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Console\Command;

class TestBookingStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:booking-status-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test booking status update functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Testing booking status update functionality...");
        
        // Get a booking to test with
        $booking = Booking::with('room')->first();
        
        if (!$booking) {
            $this->error("No bookings found to test with.");
            return;
        }
        
        $this->info("Testing with booking: {$booking->booking_reference}");
        $this->info("Current booking status: {$booking->status}");
        $this->info("Room: {$booking->room->room_number} ({$booking->room->name})");
        $this->info("Room status: {$booking->room->status}");
        $this->info("Room occupancy: {$booking->room->occupied}/{$booking->room->capacity}");
        
        // Test status updates
        $statuses = ['confirmed', 'checked_in', 'checked_out'];
        
        foreach ($statuses as $status) {
            if ($booking->status !== $status) {
                $this->info("\n--- Updating booking to: {$status} ---");
                
                $oldRoomStatus = $booking->room->status;
                $oldRoomOccupied = $booking->room->occupied;
                
                $booking->update(['status' => $status]);
                
                // Refresh room data
                $room = $booking->room->fresh();
                
                $this->info("Room status: {$oldRoomStatus} → {$room->status}");
                $this->info("Room occupancy: {$oldRoomOccupied} → {$room->occupied}");
                
                // Update booking status for next iteration
                $booking = $booking->fresh();
            }
        }
        
        $this->info("\n✅ Booking status update test completed!");
    }
}