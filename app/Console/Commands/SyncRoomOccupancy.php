<?php

namespace App\Console\Commands;

use App\Models\Room;
use Illuminate\Console\Command;

class SyncRoomOccupancy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rooms:sync-occupancy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync room occupancy with actual bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Syncing room occupancy with actual bookings...");
        
        $rooms = Room::all();
        $updatedCount = 0;
        
        foreach ($rooms as $room) {
            $oldOccupancy = $room->occupied;
            $oldStatus = $room->status;
            
            // Calculate actual occupancy from bookings
            $actualOccupancy = $room->calculateActualOccupancy();
            
            // Update room occupancy and status
            $room->update(['occupied' => $actualOccupancy]);
            $room->updateStatusBasedOnOccupancy();
            
            $newOccupancy = $room->fresh()->occupied;
            $newStatus = $room->fresh()->status;
            
            if ($oldOccupancy !== $newOccupancy || $oldStatus !== $newStatus) {
                $this->info("Room {$room->room_number}: occupancy {$oldOccupancy}→{$newOccupancy}, status {$oldStatus}→{$newStatus}");
                $updatedCount++;
            }
        }
        
        $this->info("\n✅ Synced {$updatedCount} rooms successfully!");
    }
}