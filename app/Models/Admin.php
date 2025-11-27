<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'name', // Alias for full_name
        'role',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'department',
        'position',
        'hire_date',
        'bio',
        'avatar',
        'telegram_bot_token',
        'telegram_chat_id',
        'telegram_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'hire_date' => 'date',
        ];
    }

    /**
     * Get the admin's name (use full_name or name)
     */
    public function getNameAttribute()
    {
        return $this->full_name ?? $this->attributes['name'] ?? $this->username ?? '';
    }

    /**
     * Set the admin's name
     */
    public function setNameAttribute($value)
    {
        $this->attributes['full_name'] = $value;
        if (isset($this->attributes['name'])) {
            $this->attributes['name'] = $value;
        }
    }
}
