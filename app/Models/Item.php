<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;    protected $fillable = [
        'nama_barang',
        'kode_barang',
        'category_id',
        'satuan',
        'stok_minimum',
        'deskripsi',
        'photo_id',
    ];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class, 'photo_id');
    }

    public function stocks()
    {
        return $this->hasMany(ItemStock::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function details()
    {
        return $this->hasMany(ItemRequestDetail::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

}
