<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashbooks', function (Blueprint $table) {
            $table->id();
            $table->string('deskripsi');
            $table->enum('type', ['IN','OUT']);
            $table->integer('nominal');
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cashbook_category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashbooks');
    }
};
