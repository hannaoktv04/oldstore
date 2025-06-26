<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['photo_product']);
            $table->dropColumn('photo_product');
            $table->foreignId('photo_id')->nullable()->constrained('item_images')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['photo_id']);
            $table->dropColumn('photo_id');

            $table->unsignedBigInteger('photo_product')->nullable();
            $table->foreign('photo_product')->references('id')->on('item_images')->nullOnDelete();
        });
    }
};