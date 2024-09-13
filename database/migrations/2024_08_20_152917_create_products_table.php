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
        Schema::create('products', function (Blueprint $table) {
            $table->id('pkProductId');
            $table->string('name');
            $table->double('price');
            $table->enum('type', ['Food', 'Beverage', 'Shake']);
            $table->enum('size', ['Small', 'Medium', 'Large', 'No Size'])->default('No Size');
            $table->enum('availability', ['Available', 'Not Available'])->default('Not Available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
