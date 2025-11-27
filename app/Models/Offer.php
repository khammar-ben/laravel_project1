<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_code',
        'name',
        'description',
        'type',
        'discount_type',
        'discount_value',
        'min_guests',
        'min_nights',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_to',
        'status',
        'is_public',
        'conditions',
        'user_id'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'conditions' => 'array',
        'is_public' => 'boolean',
        'discount_value' => 'decimal:2'
    ];

    // Generate unique offer code
    public static function generateOfferCode()
    {
        do {
            $code = 'OFF' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('offer_code', $code)->exists());
        
        return $code;
    }

    // Check if offer is currently valid
    public function isValid()
    {
        $now = now()->toDateString();
        return $this->status === 'active' && 
               $this->valid_from <= $now && 
               $this->valid_to >= $now &&
               ($this->max_uses === null || $this->used_count < $this->max_uses);
    }

    // Get formatted discount display
    public function getFormattedDiscountAttribute()
    {
        switch ($this->discount_type) {
            case 'percentage':
                return $this->discount_value . '%';
            case 'fixed_amount':
                return '$' . number_format($this->discount_value, 2);
            case 'free_night':
                return 'Free ' . $this->discount_value . ' night(s)';
            default:
                return $this->discount_value;
        }
    }

    // Get usage percentage
    public function getUsagePercentageAttribute()
    {
        if ($this->max_uses === null) return 0;
        return round(($this->used_count / $this->max_uses) * 100, 1);
    }

    // Scope for active offers
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('valid_from', '<=', now()->toDateString())
                    ->where('valid_to', '>=', now()->toDateString());
    }

    // Scope for public offers
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
