<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'status', 'keterangan', 'tanggal_permintaan','tanggal_pengambilan',
        'approved_by', 'approved_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function approvedBy() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details() {
        return $this->hasMany(ItemRequestDetail::class);
    }
    public function itemDelivery()
    {
        return $this->hasOne(ItemDelivery::class, 'item_request_id');
    }

}