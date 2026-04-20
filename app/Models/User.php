<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Get the transactions for the user (cashier).
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'cashier_id');
    }

    /**
     * Get the sales for the user.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id');
    }

    /**
     * Get the carts for the user.
     */
    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    // ─── Role Helper Methods ──────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function canManageUsers(): bool
    {
        return in_array($this->role, ['admin', 'manager'], true);
    }

    public function canManageInventory(): bool
    {
        return in_array($this->role, ['admin', 'manager'], true);
    }

    public function canViewReports(): bool
    {
        return in_array($this->role, ['admin', 'manager'], true);
    }
}
