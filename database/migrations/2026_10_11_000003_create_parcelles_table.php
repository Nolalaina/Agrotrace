<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcelles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained()->restrictOnDelete();
            $table->string('code', 30);
            $table->string('nom');
            $table->decimal('superficie_ha', 8, 2);
            $table->string('localisation')->nullable();
            $table->string('variete');
            $table->string('etat')->default('productive');
            $table->date('date_creation')->nullable();
            $table->timestamps();

            $table->unique(['exploitation_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelles');
    }
};
