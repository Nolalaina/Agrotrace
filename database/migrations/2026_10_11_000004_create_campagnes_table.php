<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campagnes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained()->restrictOnDelete();
            $table->foreignId('parcelle_id')->constrained()->restrictOnDelete();
            $table->string('libelle');
            $table->date('date_debut');
            $table->date('date_fin_prevue');
            $table->string('statut')->default('en_cours'); // planifiee | en_cours | terminee
            $table->timestamps();

            $table->index(['exploitation_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campagnes');
    }
};
