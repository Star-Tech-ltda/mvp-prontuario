<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * essa tabela serve para armazenar somente os sinais vitais e medidas fisicas do paciente.
     */
    public function up(): void
    {
        Schema::create('biometric_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evolution_id')->constrained()->onDelete('cascade');
            $table->decimal('height', 8, 2)->nullable();//altura
            $table->decimal('weight', 8, 2)->nullable();//peso
            $table->integer('age')->nullable();//idade
            $table->integer('systolic_pressure')->nullable();//pressão sistolica
            $table->integer('diastolic_pressure')->nullable();//pressão diastolica
            $table->integer('heart_rate')->nullable();//frequencia cardiaca
            $table->integer('respiratory_rate')->nullable();//frequencia respiratoria
            $table->integer('oxygen_saturation')->nullable();//saturação
            $table->integer('temperature')->nullable();//temperatura
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_data');
    }
};
