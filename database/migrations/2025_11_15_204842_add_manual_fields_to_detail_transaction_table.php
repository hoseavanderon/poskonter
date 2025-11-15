<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('detail_transaction', function (Blueprint $table) {
            $table->string('manual_name')->nullable()->after('product_id');
            $table->string('item_type')->default('product')->after('manual_name'); // product | service
        });
    }

    public function down()
    {
        Schema::table('detail_transaction', function (Blueprint $table) {
            $table->dropColumn(['manual_name', 'item_type']);
        });
    }
};
