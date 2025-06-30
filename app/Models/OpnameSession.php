<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpnameSession extends Model
{
    use HasFactory;
    public $timestamps = true; 

    protected $fillable = [
        'periode_bulan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'block_transaction',
        'dibuka_oleh',
        'catatan'
    ];

    protected $casts = [
        'block_transaction' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date'
    ];

    // Relasi ke user yang membuka sesi
    public function opener()
    {
        return $this->belongsTo(User::class, 'dibuka_oleh');
    }

    // Relasi many-to-many dengan Item melalui tabel stock_opnames
    public function items()
    {
        return $this->belongsToMany(Item::class, 'stock_opnames')
            ->using(StockOpname::class)
            ->withPivot([
                'qty_sistem',
                'qty_fisik',
                'selisih',
                'status',
                'dilakukan_oleh',
                'tanggal_opname',
                'catatan'
            ]);
    }

    // Scope untuk sesi aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }
}