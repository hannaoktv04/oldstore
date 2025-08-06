<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_barang',
        'kode_barang',
        'category_id',
        'satuan_id',
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

    public function stocks()
    {
        return $this->hasOne(ItemStock::class, 'item_id');
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
        return $this->hasOne(ItemState::class, 'item_id');
    }

    public function purchaseOrderDetails()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function logs()
    {
        return $this->hasMany(ItemLog::class, 'item_id');
    }
    public function getGalleryAttribute()
    {
        $gallery = collect();

        if ($this->photo && $this->photo->image) {
            $gallery->push($this->photo->image);
        }

        foreach ($this->images as $image) {
            if (!$this->photo || $image->id !== $this->photo->id) {
                $gallery->push($image->image);
            }
        }

        if ($gallery->isEmpty()) {
            $gallery->push('assets/img/default.png');
        }

        return $gallery;
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo && $this->photo->image
            ? $this->photo->image
            : 'assets/img/default.png';
    }

    public function getTotalStokAttribute()
    {
        return $this->stocks?->qty ?? 0;
    }

    public function stockNotifications()
    {
        return $this->hasMany(StockNotification::class);
    }
    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class, 'item_id');
    }
    public function adjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'item_id');
    }
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }
}