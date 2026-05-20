<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LotReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_id', 'reporter_id', 'reason'
    ];

    public function lot() { return $this->belongsTo(Lot::class); }
    public function reporter() { return $this->belongsTo(User::class, 'reporter_id'); }
}
