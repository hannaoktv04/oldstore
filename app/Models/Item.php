<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item_images;
use App\Models\ItemStock;

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

    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

     public function photo()
    {
        return $this->belongsTo(ItemImage::class, 'photo_id');
    }


    public function getPhotoUrlAttribute()
{
    return $this->gallery->first() ?? 'assets/img/default.png';
}

    public function getGalleryAttribute()
    {
        $gallery = collect();
        if ($this->photo && $this->photo->image) {
            $gallery->push($this->photo->image);
        }
        foreach ($this->images as $image) {
            if ((!$this->photo || $image->id !== $this->photo->id) && $image->image) {
                $gallery->push($image->image);
            }
        }
        if ($gallery->isEmpty()) {
            $gallery->push('assets/img/default.png');
        }
        return $gallery;
    }
    public function stocks()
    {
        return $this->hasOne(ItemStock::class);
    }
    public function getTotalStokAttribute()
    {
        return $this->stocks()->sum('qty');
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
    public function state()
    {
        return $this->hasOne(ItemState::class);
    }
    public function purchaseOrderDetails()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

}
