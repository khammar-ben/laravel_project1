<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'name',
        'type',
        'capacity',
        'occupied',
        'floor',
        'price',
        'status',
        'description',
        'image_url',
        'amenities',
        'last_cleaned',
        'user_id',
    ];

    protected $casts = [
        'amenities' => 'array',
        'last_cleaned' => 'datetime',
        'price' => 'decimal:2',
    ];

    // Accessor for availability
    public function getAvailableAttribute()
    {
        return $this->occupied < $this->capacity;
    }

    // Accessor for occupancy percentage
    public function getOccupancyPercentageAttribute()
    {
        if ($this->capacity === 0) return 0;
        return round(($this->occupied / $this->capacity) * 100);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the admin who owns this room
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Update room status based on current occupancy
     */
    public function updateStatusBasedOnOccupancy(): void
    {
        if ($this->occupied >= $this->capacity) {
            $this->update(['status' => 'full']);
        } elseif ($this->occupied > 0) {
            $this->update(['status' => 'occupied']);
        } else {
            $this->update(['status' => 'available']);
        }
    }

    /**
     * Check if room is available for booking
     */
    public function isAvailableForBooking(): bool
    {
        return in_array($this->status, ['available', 'occupied']) && $this->occupied < $this->capacity;
    }

    /**
     * Check if room can accommodate additional guests
     */
    public function canAccommodate(int $additionalGuests): bool
    {
        return ($this->occupied + $additionalGuests) <= $this->capacity;
    }

    /**
     * Get available beds/spaces in the room
     */
    public function getAvailableSpaces(): int
    {
        return max(0, $this->capacity - $this->occupied);
    }

    /**
     * Calculate occupancy based on active bookings
     */
    public function calculateActualOccupancy(): int
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->sum('number_of_guests');
    }

    /**
     * Sync occupancy with actual bookings
     */
    public function syncOccupancy(): void
    {
        $actualOccupancy = $this->calculateActualOccupancy();
        $this->update(['occupied' => $actualOccupancy]);
        $this->updateStatusBasedOnOccupancy();
    }

    /**
     * Get room availability status with detailed information
     */
    public function getAvailabilityStatus(): array
    {
        return [
            'room_id' => $this->id,
            'room_number' => $this->room_number,
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'capacity' => $this->capacity,
            'occupied' => $this->occupied,
            'available_spaces' => $this->getAvailableSpaces(),
            'occupancy_percentage' => $this->occupancy_percentage,
            'is_available' => $this->isAvailableForBooking(),
            'last_cleaned' => $this->last_cleaned,
            'needs_cleaning' => $this->last_cleaned ? 
                $this->last_cleaned->diffInDays(now()) >= 7 : true,
            'floor' => $this->floor,
            'price' => $this->price
        ];
    }

    /**
     * Get current active bookings for this room
     */
    public function getActiveBookings()
    {
        return $this->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->with('guest')
            ->get();
    }

    /**
     * Get upcoming bookings for this room
     */
    public function getUpcomingBookings(int $days = 7)
    {
        return $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in_date', '>=', now())
            ->where('check_in_date', '<=', now()->addDays($days))
            ->with('guest')
            ->orderBy('check_in_date')
            ->get();
    }

    /**
     * Get room maintenance status
     */
    public function getMaintenanceStatus(): array
    {
        $daysSinceCleaned = $this->last_cleaned ? 
            $this->last_cleaned->diffInDays(now()) : null;

        return [
            'status' => $this->status,
            'needs_maintenance' => $this->status === 'maintenance',
            'needs_cleaning' => $daysSinceCleaned === null || $daysSinceCleaned >= 7,
            'days_since_cleaned' => $daysSinceCleaned,
            'last_cleaned' => $this->last_cleaned,
            'cleaning_due' => $this->last_cleaned ? 
                $this->last_cleaned->addDays(7) : null
        ];
    }

    /**
     * Mark room as cleaned
     */
    public function markAsCleaned(): bool
    {
        return $this->update([
            'last_cleaned' => now(),
            'status' => $this->occupied > 0 ? 'occupied' : 'available'
        ]);
    }

    /**
     * Set room to maintenance mode
     */
    public function setMaintenanceMode(string $reason = null): bool
    {
        return $this->update([
            'status' => 'maintenance',
            'description' => $reason ? 
                ($this->description . "\nMaintenance: " . $reason) : 
                $this->description
        ]);
    }

    /**
     * Remove from maintenance mode
     */
    public function removeMaintenanceMode(): bool
    {
        $newStatus = $this->occupied >= $this->capacity ? 'full' : 
                    ($this->occupied > 0 ? 'occupied' : 'available');
        
        return $this->update(['status' => $newStatus]);
    }
}
