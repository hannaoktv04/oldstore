<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemWishlist extends Model
{
    protected $fillable = [
        'user_id', 'nama_barang', 'deskripsi', 'category_id',
        'qty_diusulkan', 'status', 'catatan_admin'
    ];
}
