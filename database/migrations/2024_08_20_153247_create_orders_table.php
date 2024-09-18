<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('pkOrderId');
            $table->unsignedBigInteger('fkTableId');
            $table->unsignedBigInteger('fkProductId');
            $table->integer('quantity')->default(0);
            //Active = Inside Cart
            //Pending = Checkout
            //Completed = Served
            $table->enum('status', ['Active', 'Pending', 'Served', 'Completed']);
            $table->foreign('fkTableId')->references('pkTableId')->on('tables')->onDelete('cascade');
            $table->foreign('fkProductId')->references('pkProductId')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
