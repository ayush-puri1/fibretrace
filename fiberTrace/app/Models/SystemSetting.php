<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key', 'value', 'type', 'description', 'updated_by'
    ];

    public function updater() { return $this->belongsTo(User::class, 'updated_by'); }

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;
        
        switch ($setting->type) {
            case 'number': return (float) $setting->value;
            case 'boolean': return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
            case 'json': return json_decode($setting->value, true);
            default: return $setting->value;
        }
    }
}
