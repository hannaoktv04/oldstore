<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'size',
        'harga',
        'stok',
        'deskripsi',
        'category_id',
        'photo_id',
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            $lastId = self::max('id') + 1;
            $item->kode_barang = 'ITM-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        });
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    public function photo()
    {
        return $this->belongsTo(ItemImage::class, 'photo_id');
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

    public function purchaseOrderDetails()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function logs()
    {
        return $this->hasMany(ItemLog::class, 'item_id');
    }

    public function sizes()
    {
        return $this->hasMany(ItemSize::class);
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

    public function getSatuanAttribute()
    {
        return 'pasang';
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
}
