<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_delivery_id')
                  ->constrained('item_deliveries')
                  ->onDelete('cascade');
            $table->foreignId('received_by')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->dateTime('tanggal_terima');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_receipts');
    }
};
