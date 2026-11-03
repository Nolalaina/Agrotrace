<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->decimal('poids_total_kg', 12, 2)->default(0);
            $table->string('statut')->default('ouvert'); // ouvert | cloture | vendu
            $table->string('qualite')->nullable();
            $table->timestamp('cloture_le')->nullable();
            $table->timestamps();

            $table->index(['exploitation_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
