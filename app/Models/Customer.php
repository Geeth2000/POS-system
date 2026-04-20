<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'loyalty_points',
        'is_active',
    ];

    protected $casts = [
        'loyalty_points' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the transactions for the customer.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Calculate total spent by customer.
     */
    public function getTotalSpentAttribute()
    {
        return $this->transactions()->sum('total_amount');
    }
}
