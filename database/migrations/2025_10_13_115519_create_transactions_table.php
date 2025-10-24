<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('subtotal');
            $table->string('dibayar');
            $table->string('kembalian');
            $table->string('nomor_nota')->unique();
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->boolean('is_lunas')->default(false);
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
