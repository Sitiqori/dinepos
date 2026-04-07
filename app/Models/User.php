<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',          // 'admin' | 'kasir'
        'phone',
        'address',
        'branch',
        'join_date',
        'profile_photo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'join_date'         => 'date',
        'is_active'         => 'boolean',
    ];

    // ── Role helpers ───────────────────────────────
    public function isAdmin(): bool  { return $this->role === 'admin'; }
    public function isKasir(): bool  { return $this->role === 'kasir'; }

    // ── Active helper (safe for old rows without column) ──
    public function getIsActiveAttribute($value): bool
    {
        return $value === null ? true : (bool) $value;
    }

    // ── Photo URL helper ───────────────────────────
    public function getPhotoUrlAttribute(): string
    {
        return $this->profile_photo
            ? asset('storage/' . $this->profile_photo)
            : '';
    }

    // ── Initials for avatar fallback ───────────────
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    // ── Relationships ──────────────────────────────
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
