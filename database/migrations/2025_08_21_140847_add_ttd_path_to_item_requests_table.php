<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->string('ttd_path')->nullable()->after('keterangan');
        });
    }


    public function down(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->dropColumn('ttd_path');
        });
    }
};
