<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions_financieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained()->restrictOnDelete();
            $table->foreignId('campagne_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('type'); // depense | recette
            $table->string('categorie');
            $table->string('libelle')->nullable();
            $table->decimal('montant', 14, 2);
            $table->date('date_transaction');
            $table->timestamps();

            $table->index(['exploitation_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions_financieres');
    }
};
