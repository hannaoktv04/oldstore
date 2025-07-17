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

    public function opener()
    {
        return $this->belongsTo(User::class, 'dibuka_oleh');
    }

       public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class, 'session_id');
    }


     public function canBeEnded()
    {
        return $this->status === 'aktif';
    }
}