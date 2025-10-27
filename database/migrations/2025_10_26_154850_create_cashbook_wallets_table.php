<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashbook_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('cashbook_wallet'); // Nama dompet, misalnya 'Main Wallet', 'Cash', dll
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashbook_wallets');
    }
};
