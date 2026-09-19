<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('vehicules', function (Blueprint $table) {
        $table->id();
        $table->string('immatriculation')->unique();
        $table->string('marque');
        $table->string('modele');
        $table->string('couleur');
        $table->year('annee');
        $table->integer('kilometrage');
        $table->string('carrosserie'); 
        $table->string('energie');
        $table->string('boite');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('vehicules');
}
};
