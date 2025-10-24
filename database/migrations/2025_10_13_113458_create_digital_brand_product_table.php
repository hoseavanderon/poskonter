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
        Schema::create('digital_brand_product', function (Blueprint $table) {
            $table->foreignId('digital_brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('digital_product_id')->constrained()->cascadeOnDelete();
            $table->primary(['digital_brand_id', 'digital_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_brand_product');
    }
};
