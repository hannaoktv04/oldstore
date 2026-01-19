<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'payment_status', 'snap_token',
        'province_code', 'city_code', 'district_code', 'village_code',
        'full_address', 'postal_code', 'courier', 'weight',
        'shipping_cost', 'subtotal', 'total_amount'
    ];

    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Item-item di dalam order ini
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
