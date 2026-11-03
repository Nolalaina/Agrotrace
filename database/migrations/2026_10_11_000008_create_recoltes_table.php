<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recoltes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained()->restrictOnDelete();
            $table->foreignId('campagne_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date_recolte');
            $table->decimal('quantite_kg', 12, 2);
            $table->decimal('quantite_disponible_kg', 12, 2); // part non encore affectée à un lot
            $table->string('calibre')->nullable(); // petit | moyen | gros
            $table->string('notes', 500)->nullable();
            $table->timestamps();

            $table->index(['campagne_id', 'date_recolte']);
            $table->index(['exploitation_id', 'quantite_disponible_kg']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE recoltes ADD CONSTRAINT chk_recoltes_dispo CHECK (quantite_disponible_kg >= 0 AND quantite_disponible_kg <= quantite_kg)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recoltes');
    }
};
