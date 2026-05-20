<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'password',
        'gstin',
        'role',
        'status',
        'rejection_reason',
        'city',
        'state',
        'address',
        'verified_at',
        'verified_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function lots() { return $this->hasMany(Lot::class, 'seller_id'); }
    public function bids() { return $this->hasMany(Bid::class, 'buyer_id'); }
    public function buyTransactions() { return $this->hasMany(Transaction::class, 'buyer_id'); }
    public function sellTransactions() { return $this->hasMany(Transaction::class, 'seller_id'); }
    
    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeVerified($query) { return $query->where('status', 'verified'); }
    public function scopeSellers($query) { return $query->where('role', 'seller'); }
    public function scopeBuyers($query) { return $query->where('role', 'buyer'); }

    public function isSeller(): bool { return $this->role === 'seller'; }
    public function isBuyer(): bool { return $this->role === 'buyer'; }
    public function isAdmin(): bool { return in_array($this->role, ['admin', 'super_admin']); }
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isSuper(): bool { return $this->role === 'super_admin'; } // Alias

    public function getMaskedPhoneAttribute(): string {
        if (!$this->phone) return '';
        return substr($this->phone, 0, 3) . '****' . substr($this->phone, -3);
    }
    
    public function getMaskedGstinAttribute(): string {
        if (!$this->gstin) return '';
        return substr($this->gstin, 0, 2) . '******' . substr($this->gstin, -4);
    }
}
