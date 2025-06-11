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
        Schema::create('item_wishlists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('nama_barang');
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('category_id'); // TIDAK nullable
            $table->double('qty_diusulkan', 15, 2)->default(1);
            $table->enum('status', ['pending', 'diakomodasi', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            // FK valid (pastikan tabel `category` sudah dimigrate!)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade'); // karena tidak nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_wishlists');
    }
};
