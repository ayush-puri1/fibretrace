<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LotImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_id', 'file_path', 'file_name', 'file_size', 'sort_order'
    ];

    public function lot() { return $this->belongsTo(Lot::class); }
}
