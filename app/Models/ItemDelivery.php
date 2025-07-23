<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_request_id',
        'operator_id',
        'tanggal_kirim',
        'status',
        'staff_pengiriman',
        'bukti_foto',
        'catatan'
    ];

    public function request()
    {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }

    public function receipts()
    {
        return $this->hasMany(ItemReceipt::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_pengiriman');
    }

}
