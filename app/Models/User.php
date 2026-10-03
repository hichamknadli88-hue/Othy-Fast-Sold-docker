<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    // personal_pwd_initialized and base_token_hash were removed from $fillable:
    // they are security-sensitive and must never be settable through mass assignment.
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'base_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'personal_pwd_initialized' => 'boolean',
        ];
    }

    public function platformAccounts(): HasMany
    {
        return $this->hasMany(UserPlatformAccount::class);
    }

    public function rechargeOrders(): HasMany
    {
        return $this->hasMany(RechargeOrder::class);
    }

    public function isAdmin(): bool
    {
        // Fail loudly instead of silently trusting a fallback address.
        $adminEmail = config('app.admin_email')
            ?? throw new \RuntimeException('ADMIN_EMAIL is not configured.');

        return strtolower($this->email) === strtolower($adminEmail);
    }
}