<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained()->restrictOnDelete();
            $table->foreignId('lot_id')->unique()->constrained()->restrictOnDelete();
            $table->string('acheteur');
            $table->decimal('quantite_kg', 12, 2);
            $table->decimal('montant', 14, 2);
            $table->date('date_vente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
