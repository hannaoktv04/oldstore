<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemState extends Model
{
    protected $fillable = ['item_id', 'is_archived'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}