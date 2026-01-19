<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('payment_status')->default('pending'); 
            $table->string('snap_token')->nullable(); 
            
            $table->string('province_code');
            $table->string('city_code');
            $table->string('district_code');
            $table->string('village_code'); 
            $table->text('full_address');
            $table->string('postal_code');

            $table->string('courier'); 
            $table->integer('weight'); 
            $table->decimal('shipping_cost', 12, 2); 
            
            $table->decimal('subtotal', 12, 2); 
            $table->decimal('total_amount', 12, 2); 
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};