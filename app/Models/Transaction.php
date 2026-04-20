<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'customer_id',
        'cashier_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the transaction.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the cashier (user) for the transaction.
     */
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Get the items for the transaction.
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Generate transaction number.
     */
    public static function generateTransactionNumber()
    {
        $prefix = 'TRX-' . date('Ymd');
        $lastTransaction = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastTransaction ? intval(substr($lastTransaction->transaction_number, -4)) + 1 : 1;
        
        return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
