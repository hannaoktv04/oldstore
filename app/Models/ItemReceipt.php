<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_delivery_id',
        'received_by',
        'tanggal_terima',
        'catatan',
    ];

    public function delivery()
    {
        return $this->belongsTo(ItemDelivery::class, 'item_delivery_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
