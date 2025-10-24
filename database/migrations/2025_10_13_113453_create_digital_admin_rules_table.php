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
        Schema::create('digital_admin_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('digital_category_id')->constrained()->cascadeOnDelete();
            $table->integer('min_nominal');
            $table->integer('max_nominal');
            $table->integer('admin_fee');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_admin_rules');
    }
};
