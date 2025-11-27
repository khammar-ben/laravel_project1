<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'price',
        'duration_minutes',
        'max_participants',
        'min_participants',
        'difficulty_level',
        'included_items',
        'requirements',
        'location',
        'meeting_point',
        'available_days',
        'start_time',
        'end_time',
        'image_url',
        'gallery_images',
        'is_active',
        'requires_booking',
        'advance_booking_hours',
        'cancellation_policy',
        'what_to_bring',
        'rating',
        'total_reviews',
        'user_id',
    ];

    protected $casts = [
        'included_items' => 'array',
        'requirements' => 'array',
        'available_days' => 'array',
        'gallery_images' => 'array',
        'is_active' => 'boolean',
        'requires_booking' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    /**
     * Get all bookings for this activity
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(ActivityBooking::class);
    }

    /**
     * Get confirmed bookings for this activity
     */
    public function confirmedBookings(): HasMany
    {
        return $this->hasMany(ActivityBooking::class)->where('status', 'confirmed');
    }

    /**
     * Get the admin who owns this activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Check if activity is available on a specific date
     */
    public function isAvailableOnDate($date): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $dayOfWeek = strtolower(date('l', strtotime($date)));
        $availableDays = $this->available_days ?? [];
        
        return in_array($dayOfWeek, $availableDays);
    }

    /**
     * Get available slots for a specific date
     */
    public function getAvailableSlotsForDate($date): int
    {
        $bookedParticipants = $this->bookings()
            ->where('booking_date', $date)
            ->whereIn('status', ['confirmed', 'pending'])
            ->sum('participants');

        return max(0, $this->max_participants - $bookedParticipants);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Get difficulty badge color
     */
    public function getDifficultyColorAttribute(): string
    {
        return match($this->difficulty_level) {
            'easy' => 'bg-green-100 text-green-800',
            'moderate' => 'bg-yellow-100 text-yellow-800',
            'hard' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Scope for active activities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for activities available today
     */
    public function scopeAvailableToday($query)
    {
        $today = strtolower(date('l'));
        return $query->active()->whereJsonContains('available_days', $today);
    }
}