<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calculated_metrics', function (Blueprint $table) {
            $table->id();
            // Tipo de cálculo realizado
            $table->enum('calculated_type', ['BMI', 'BloodPressure', 'HeartRate', 'RespiratoryRate', 'OxygenSaturation', 'Temperature'])
                ->nullable()
                ->comment('Define qual o tipo de calculo foi feito');
            $table->string('result')->nullable();//resultado do calculo
            $table->string('interpretation')->nullable();// interpretacao feita automaticamente atraves do resultado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculated_metrics');
    }
};
