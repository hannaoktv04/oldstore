<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->double('qty_sebelum', 15, 2);
            $table->double('qty_fisik', 15, 2);
            $table->double('qty_selisih', 15, 2);
            $table->enum('tipe_adjustment', ['opname', 'koreksi', 'stok awal']);
            $table->text('keterangan')->nullable();
            $table->foreignId('adjusted_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('adjusted_at');
            $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
