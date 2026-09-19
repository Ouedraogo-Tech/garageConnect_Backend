<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('reparations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('vehicule_id')->constrained()->cascadeOnDelete();
        $table->date('date');
        $table->decimal('duree_main_oeuvre', 5, 2);
        $table->string('objet_reparation');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('reparations');
}
};
