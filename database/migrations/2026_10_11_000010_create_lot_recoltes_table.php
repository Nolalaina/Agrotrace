<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lot_recoltes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
            // restrictOnDelete : une récolte déjà affectée à un lot ne peut pas être supprimée.
            $table->foreignId('recolte_id')->constrained()->restrictOnDelete();
            $table->decimal('quantite_kg', 12, 2);
            $table->timestamps();

            $table->unique(['lot_id', 'recolte_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lot_recoltes');
    }
};
