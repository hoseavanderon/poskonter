<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('apps', function (Blueprint $table) {
            // logo disimpan path relatif (storage/app/public/...), nullable
            $table->string('logo')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};
