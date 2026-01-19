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
        Schema::create('item_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')
                ->constrained('items')
                ->onDelete('cascade');

            $table->string('size', 50); // S, M, L, XL, dll
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_sizes');
    }
};
