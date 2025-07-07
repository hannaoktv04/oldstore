<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_deliveries', function (Blueprint $table) {
            $table->string('staff_pengiriman')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('item_deliveries', function (Blueprint $table) {
            $table->dropColumn('staff_pengiriman');
        });
    }
};
