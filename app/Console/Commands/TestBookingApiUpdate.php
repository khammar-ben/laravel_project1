<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestBookingApiUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:booking-api-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test booking status update through API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Testing booking status update through API...");
        
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
        
        // Test updating booking status through API
        $baseUrl = 'http://localhost:8000';
        
        try {
            // Get CSRF cookie first
            $this->info("\n--- Getting CSRF cookie ---");
            $csrfResponse = Http::get("{$baseUrl}/sanctum/csrf-cookie");
            $this->info("CSRF cookie obtained: " . ($csrfResponse->successful() ? 'Yes' : 'No'));
            
            // Test updating booking status
            $this->info("\n--- Testing booking status update ---");
            
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->put("{$baseUrl}/api/bookings/{$booking->id}", [
                'status' => 'checked_in'
            ]);
            
            if ($response->successful()) {
                $this->info("✅ Booking status updated successfully!");
                $responseData = $response->json();
                $this->info("New booking status: {$responseData['data']['status']}");
                
                // Check room status after update
                $room = Room::find($booking->room_id);
                $this->info("Room status after update: {$room->status}");
                $this->info("Room occupancy after update: {$room->occupied}/{$room->capacity}");
            } else {
                $this->error("❌ Failed to update booking status");
                $this->error("Status: " . $response->status());
                $this->error("Response: " . $response->body());
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error testing API: " . $e->getMessage());
        }
        
        $this->info("\n✅ API test completed!");
    }
}
