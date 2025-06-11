<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
        'stok_minimum',
        'deskripsi',
        'image',
        'kategori'
    ];

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

}
