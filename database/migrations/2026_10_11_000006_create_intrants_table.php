<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intrants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained()->restrictOnDelete();
            $table->string('nom');
            $table->string('categorie'); // engrais | phytosanitaire | emballage | carburant | consommable
            $table->string('unite', 20)->default('kg');
            $table->decimal('quantite_disponible', 12, 2)->default(0);
            $table->decimal('seuil_minimum', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['exploitation_id', 'nom']);
        });

        // Garde-fou en base : le stock ne peut jamais être négatif (SQLite ne gère pas ADD CONSTRAINT).
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE intrants ADD CONSTRAINT chk_intrants_stock_positif CHECK (quantite_disponible >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('intrants');
    }
};
