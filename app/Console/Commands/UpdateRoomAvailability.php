<?php

namespace App\Console\Commands;

use App\Services\RoomAvailabilityService;
use Illuminate\Console\Command;

class UpdateRoomAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rooms:update-availability 
                            {--room-id= : Update specific room ID}
                            {--force : Force update even if not needed}
                            {--maintenance : Check for rooms needing maintenance}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update room availability status and sync occupancy with actual bookings';

    protected RoomAvailabilityService $availabilityService;

    public function __construct(RoomAvailabilityService $availabilityService)
    {
        parent::__construct();
        $this->availabilityService = $availabilityService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting room availability update...');

        $roomId = $this->option('room-id');
        $force = $this->option('force');
        $checkMaintenance = $this->option('maintenance');

        if ($roomId) {
            $this->updateSpecificRoom($roomId);
        } else {
            $this->updateAllRooms($force);
        }

        if ($checkMaintenance) {
            $this->checkMaintenanceNeeds();
        }

        $this->info('Room availability update completed!');
    }

    private function updateSpecificRoom(int $roomId): void
    {
        $this->info("Updating room ID: {$roomId}");

        $updated = $this->availabilityService->updateRoomStatus($roomId);
        
        if ($updated) {
            $this->info("✓ Room {$roomId} status updated successfully");
        } else {
            $this->error("✗ Failed to update room {$roomId} status");
        }
    }

    private function updateAllRooms(bool $force): void
    {
        $this->info('Updating all room statuses...');

        $updatedCount = $this->availabilityService->updateAllRoomStatuses();
        
        $this->info("✓ Updated {$updatedCount} room statuses");

        // Show occupancy summary
        $summary = $this->availabilityService->getRoomOccupancySummary();
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Rooms', $summary['total_rooms']],
                ['Total Capacity', $summary['total_capacity']],
                ['Total Occupied', $summary['total_occupied']],
                ['Total Available', $summary['total_available']],
                ['Occupancy Rate', $summary['occupancy_rate'] . '%'],
            ]
        );

        // Show rooms by status
        $this->info("\nRooms by Status:");
        foreach ($summary['rooms_by_status'] as $status => $count) {
            $this->line("  {$status}: {$count}");
        }
    }

    private function checkMaintenanceNeeds(): void
    {
        $this->info('Checking rooms needing attention...');

        $roomsNeedingAttention = $this->availabilityService->getRoomsNeedingAttention();

        if ($roomsNeedingAttention->isEmpty()) {
            $this->info('✓ All rooms are in good condition');
            return;
        }

        $this->warn("Found {$roomsNeedingAttention->count()} rooms needing attention:");

        $tableData = [];
        foreach ($roomsNeedingAttention as $roomData) {
            $room = $roomData['room'];
            $tableData[] = [
                $room->room_number,
                $room->name,
                $room->status,
                $roomData['needs_cleaning'] ? 'Yes' : 'No',
                $roomData['days_since_cleaned'] ?? 'Never',
                $roomData['needs_maintenance'] ? 'Yes' : 'No'
            ];
        }

        $this->table(
            ['Room #', 'Name', 'Status', 'Needs Cleaning', 'Days Since Cleaned', 'Needs Maintenance'],
            $tableData
        );
    }
}