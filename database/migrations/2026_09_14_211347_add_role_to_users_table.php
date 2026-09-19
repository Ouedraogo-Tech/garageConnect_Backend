<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('technicien');
        $table->foreignId('technicien_id')->nullable()->constrained('techniciens')->nullOnDelete();
    });
}

    public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['technicien_id']);
        $table->dropColumn(['role', 'technicien_id']);
    });
}
};
