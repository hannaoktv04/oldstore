<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemWishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_barang',
        'deskripsi',
        'category_id',
        'qty_diusulkan',
        'status',
        'catatan_admin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
