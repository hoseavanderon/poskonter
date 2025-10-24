<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('shelves_id')->nullable()->after('supplier_id');

            // jika ingin foreign key constraint
            $table->foreign('shelves_id')
                  ->references('id')
                  ->on('shelves')
                  ->onDelete('set null'); // atau 'cascade' jika ingin otomatis hapus
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['shelves_id']);
            $table->dropColumn('shelves_id');
        });
    }
};
