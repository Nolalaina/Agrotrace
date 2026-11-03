<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained()->restrictOnDelete();
            $table->foreignId('intrant_id')->constrained()->restrictOnDelete();
            $table->foreignId('campagne_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // stock_initial | entree | sortie | consommation | ajustement
            $table->decimal('quantite', 12, 2);
            $table->decimal('stock_apres', 12, 2);
            $table->string('commentaire')->nullable();
            $table->timestamps();

            $table->index(['intrant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
