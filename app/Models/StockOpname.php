<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StockOpname extends Pivot
{
    protected $table = 'stock_opnames';

    protected $casts = [
        'qty_sistem' => 'decimal:2',
        'qty_fisik' => 'decimal:2',
        'selisih' => 'decimal:2',
        'tanggal_opname' => 'date'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function session()
    {
        return $this->belongsTo(OpnameSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }
}