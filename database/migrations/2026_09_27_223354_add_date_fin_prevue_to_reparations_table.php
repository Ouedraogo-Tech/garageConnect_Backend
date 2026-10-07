<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reparations', function (Blueprint $table) {
            $table->date('date_fin_prevue')->nullable()->after('date');
            $table->date('date_fin_reelle')->nullable()->after('date_fin_prevue');
        });
    }

    public function down()
    {
        Schema::table('reparations', function (Blueprint $table) {
            $table->dropColumn(['date_fin_prevue', 'date_fin_reelle']);
        });
    }
};
