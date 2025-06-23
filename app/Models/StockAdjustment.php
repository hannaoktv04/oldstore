<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'qty_sebelum',
        'qty_fisik',
        'qty_selisih',
        'tipe_adjustment',
        'keterangan',
        'adjusted_by',
        'adjusted_at',
    ];
}