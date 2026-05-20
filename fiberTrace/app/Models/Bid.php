<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'lot_id', 'buyer_id', 'amount', 'status'
    ];

    public function lot() { return $this->belongsTo(Lot::class); }
    public function buyer() { return $this->belongsTo(User::class, 'buyer_id'); }
}
