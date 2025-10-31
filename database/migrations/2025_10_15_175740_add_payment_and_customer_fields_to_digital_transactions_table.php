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
        Schema::table('digital_transactions', function (Blueprint $table) {
            // Tambah kolom relasi customer
            $table->foreignId('customer_id')->nullable()->after('app_id')->constrained()->nullOnDelete();

            // Tambah kolom pembayaran
            $table->string('subtotal');
            $table->string('dibayar')->nullable()->after('subtotal');
            $table->string('kembalian')->nullable()->after('dibayar');

            // Kolom waktu pelunasan
            $table->timestamp('paid_at')->nullable()->after('outlet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digital_transactions', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn([
                'customer_id',
                'subtotal',
                'dibayar',
                'kembalian',
                'paid_at',
            ]);
        });
    }
};
