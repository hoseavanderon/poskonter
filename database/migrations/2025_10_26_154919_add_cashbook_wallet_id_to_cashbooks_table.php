<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashbooks', function (Blueprint $table) {
            $table->foreignId('cashbook_wallet_id')
                ->nullable()
                ->constrained('cashbook_wallets')
                ->nullOnDelete()
                ->after('cashbook_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('cashbooks', function (Blueprint $table) {
            $table->dropForeign(['cashbook_wallet_id']);
            $table->dropColumn('cashbook_wallet_id');
        });
    }
};
