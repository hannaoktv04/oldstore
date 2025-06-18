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
        'bukti_foto',
        'status',
    ];

    public function request()
    {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function receipts()
    {
        return $this->hasMany(ItemReceipt::class);
    }
    
}