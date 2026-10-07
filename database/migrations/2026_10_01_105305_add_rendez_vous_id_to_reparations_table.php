<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reparations', function (Blueprint $table) {
            $table->foreignId('rendez_vous_id')->nullable()->after('vehicule_id')
                ->constrained('rendez_vous')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('reparations', function (Blueprint $table) {
            $table->dropForeign(['rendez_vous_id']);
            $table->dropColumn('rendez_vous_id');
        });
    }
};
