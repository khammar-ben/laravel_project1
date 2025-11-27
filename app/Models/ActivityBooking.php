<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ActivityBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'guest_id',
        'booking_reference',
        'booking_date',
        'booking_time',
        'participants',
        'total_amount',
        'per_person_price',
        'status',
        'special_requests',
        'participant_details',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
        'refund_amount',
        'admin_notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime:H:i',
        'participant_details' => 'array',
        'total_amount' => 'decimal:2',
        'per_person_price' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Boot method to generate booking reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = 'ACT-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the activity for this booking
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the guest for this booking
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'completed' => 'bg-blue-100 text-blue-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        if (in_array($this->status, ['cancelled', 'completed'])) {
            return false;
        }

        $bookingDateTime = $this->booking_date->format('Y-m-d') . ' ' . $this->booking_time->format('H:i:s');
        $bookingTimestamp = strtotime($bookingDateTime);
        $now = time();
        $advanceHours = $this->activity->advance_booking_hours ?? 24;

        return ($bookingTimestamp - $now) > ($advanceHours * 3600);
    }

    /**
     * Confirm the booking
     */
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Cancel the booking
     */
    public function cancel($reason = null, $refundAmount = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'refund_amount' => $refundAmount,
        ]);
    }

    /**
     * Complete the booking
     */
    public function complete()
    {
        $this->update([
            'status' => 'completed',
        ]);
    }

    /**
     * Scope for confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for bookings on a specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->where('booking_date', $date);
    }
}