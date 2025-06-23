<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::rename('photos', 'item_images');

        Schema::table('item_images', function (Blueprint $table) {
            $table->dropColumn(['img_xl', 'img_l', 'img_m', 'img_s']);
        });
    }

    public function down(): void
    {
        Schema::table('item_images', function (Blueprint $table) {
            $table->string('img_xl')->nullable();
            $table->string('img_l')->nullable();
            $table->string('img_m')->nullable();
            $table->string('img_s')->nullable();
        });
        Schema::rename('item_images', 'photos');
    }
};
