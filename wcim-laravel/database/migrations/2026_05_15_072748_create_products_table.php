<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('type');
            $table->string('product');
            $table->string('brand');
            $table->string('size');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('unit');
            $table->decimal('cost_per_unit', 10, 2)->default(0);
            $table->string('product_id')->nullable();
            $table->string('code');
            $table->integer('requirement')->default(0);
            $table->integer('stock')->default(0);
            $table->string('input_mode')->default('unit');
            $table->date('expiry')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
