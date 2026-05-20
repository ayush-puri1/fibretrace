<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'lot_number', 'category', 'fiber_type', 'fiber_purity_pct',
        'color_sorted', 'color_description', 'weight_kg', 'base_price', 'status',
        'highest_bid_id', 'auction_ends_at', 'flagged', 'flag_count'
    ];

    protected function casts(): array {
        return [
            'color_sorted' => 'boolean',
            'flagged' => 'boolean',
            'auction_ends_at' => 'datetime',
        ];
    }

    public function seller() { return $this->belongsTo(User::class, 'seller_id'); }
    public function bids() { return $this->hasMany(Bid::class); }
    public function images() { return $this->hasMany(LotImage::class)->orderBy('sort_order'); }
    public function highestBid() { return $this->belongsTo(Bid::class, 'highest_bid_id'); }
    public function transaction() { return $this->hasOne(Transaction::class); }
    public function reports() { return $this->hasMany(LotReport::class); }

    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeByFiber($query, $fiber) { return $query->where('fiber_type', $fiber); }
    public function scopeExpired($query) { return $query->where('auction_ends_at', '<', now()); }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->lot_number)) {
                // Generate a unique lot number like FT-1042...
                $model->lot_number = 'FT-' . substr(strtoupper(uniqid()), -6);
            }
        });
    }
}
