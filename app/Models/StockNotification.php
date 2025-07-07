<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'seen',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
