<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'image',
        'img_xl',
        'img_l',
        'img_m',
        'img_s',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
