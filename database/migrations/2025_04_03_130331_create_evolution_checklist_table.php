<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * essa tabela serve para armazenar as opções de avaliação selecionadas em cada evolução do paciente
     */
    public function up(): void
    {
        Schema::create('evolution_checklist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evolution_id')->constrained()->onDelete('cascade');
            $table->foreignId('assessment_option_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evolution_checklist');
    }
};
