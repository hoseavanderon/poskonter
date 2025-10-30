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
            $table->string('cashbook_wallet'); 
            $table->unsignedBigInteger('outlet_id')->nullable()->index();
            $table->foreign('outlet_id')
                ->references('id')
                ->on('outlets')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('cashbook_wallets', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
        });

        Schema::dropIfExists('cashbook_wallets');
    }
};
