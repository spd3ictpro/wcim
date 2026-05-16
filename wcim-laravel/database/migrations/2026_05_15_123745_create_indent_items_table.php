<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indent_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indent_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable();
            $table->string('product_name');
            $table->string('code');
            $table->string('order_display');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indent_items');
    }
};
