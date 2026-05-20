<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiber_category', 'sub_label', 'price_per_kg', 'previous_price',
        'week_start', 'published_by'
    ];

    protected function casts(): array {
        return [
            'week_start' => 'date',
        ];
    }

    public function publisher() { return $this->belongsTo(User::class, 'published_by'); }

    // Static helper matching Phase 5 logic
    public static function latestForFiber($fiberType) {
        $price = static::where('fiber_category', $fiberType)->orderBy('week_start', 'desc')->first();
        if (!$price) {
            // Default mock object if no market price exists
            $price = new static(['price_per_kg' => 35.00]);
        }
        return $price;
    }
}
