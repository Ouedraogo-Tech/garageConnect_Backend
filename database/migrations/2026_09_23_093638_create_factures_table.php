<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reparation_id')->constrained()->cascadeOnDelete();
            $table->string('numero')->unique();
            $table->decimal('montant_main_oeuvre', 10, 2);
            $table->decimal('montant_pieces', 10, 2);
            $table->decimal('montant_total', 10, 2);
            $table->enum('statut', ['impayee', 'payee'])->default('impayee');
            $table->date('date_emission');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
