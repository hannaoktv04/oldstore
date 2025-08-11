<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_notifications', function (Blueprint $table) {
            $table->string('judul')->nullable()->after('item_id');
            $table->text('pesan')->nullable()->after('judul');
            $table->string('url')->nullable()->after('pesan');
        });
    }

    public function down(): void
    {
        Schema::table('stock_notifications', function (Blueprint $table) {
            $table->dropColumn(['judul', 'pesan', 'url']);
        });
    }

};
