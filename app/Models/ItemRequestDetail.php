<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemRequestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_request_id', 'item_id', 'qty_requested', 'qty_approved'
    ];

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function request() {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }
}
