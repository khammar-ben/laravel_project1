<?php

namespace App\Console\Commands;

use App\Models\Room;
use Illuminate\Console\Command;

class FixRoomStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rooms:fix-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix room statuses based on current occupancy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rooms = Room::all();
        
        $this->info("Fixing room statuses based on occupancy...");
        
        foreach ($rooms as $room) {
            $oldStatus = $room->status;
            $room->updateStatusBasedOnOccupancy();
            $newStatus = $room->fresh()->status;
            
            if ($oldStatus !== $newStatus) {
                $this->info("Room {$room->room_number} ({$room->name}): {$oldStatus} → {$newStatus} (occupied: {$room->occupied}/{$room->capacity})");
            }
        }
        
        $this->info("Room statuses updated successfully!");
    }
}