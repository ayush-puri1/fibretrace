<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number', 'lot_id', 'bid_id', 'buyer_id', 'seller_id',
        'agreed_price', 'actual_weight_kg', 'subtotal', 'commission_amount',
        'total_amount', 'payment_status', 'dispute_reason', 'logistics_status',
        'escrow_released_at'
    ];

    protected function casts(): array {
        return [
            'escrow_released_at' => 'datetime',
        ];
    }

    public function lot() { return $this->belongsTo(Lot::class); }
    public function bid() { return $this->belongsTo(Bid::class); }
    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller() { return $this->belongsTo(User::class, 'seller_id'); }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->transaction_number)) {
                $model->transaction_number = 'TRX-' . substr(strtoupper(uniqid()), -6);
            }
        });
    }
}
