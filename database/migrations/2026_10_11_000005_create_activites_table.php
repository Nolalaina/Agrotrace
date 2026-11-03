<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained()->restrictOnDelete();
            $table->foreignId('campagne_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('type');
            $table->date('date');
            $table->text('description')->nullable();
            $table->decimal('cout', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['exploitation_id', 'date']);
            $table->index(['campagne_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activites');
    }
};
