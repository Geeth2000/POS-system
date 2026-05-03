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
        'phone_number',
        'address',
        'loyalty_points',
        'total_spend',
        'is_active',
    ];

    protected $casts = [
        'loyalty_points' => 'integer',
        'total_spend' => 'decimal:2',
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
     * Get the sales for the customer.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Sri Lankan Standard Loyalty Point Calculation:
     * 1 Point for every Rs. 100 spent.
     */
    public static function calculatePoints(float $amount): int
    {
        return (int) floor($amount / 100);
    }
}
