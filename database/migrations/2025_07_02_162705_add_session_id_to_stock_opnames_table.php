<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->after('item_id');
            $table->foreign('session_id')->references('id')->on('opname_sessions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn('session_id');
        });
    }
};