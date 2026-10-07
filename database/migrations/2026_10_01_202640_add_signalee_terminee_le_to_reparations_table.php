<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reparations', function (Blueprint $table) {
            $table->timestamp('signalee_terminee_le')->nullable()->after('date_fin_prevue');
        });
    }

    public function down()
    {
        Schema::table('reparations', function (Blueprint $table) {
            $table->dropColumn('signalee_terminee_le');
        });
    }
};
