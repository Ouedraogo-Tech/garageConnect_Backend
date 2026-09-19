<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('reparation_technicien', function (Blueprint $table) {
        $table->id();
        $table->foreignId('reparation_id')->constrained()->cascadeOnDelete();
        $table->foreignId('technicien_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('reparation_technicien');
}
};
