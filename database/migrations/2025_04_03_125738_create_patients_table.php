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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // nome do paciente
            $table->date('birth_date')->nullable(); // data de nascimento pode ser nulo
            $table->string('cpf',14)->unique()->nullable();
            $table->enum('sex', ['Male', 'Female'])->nullable(); // sexo biologico do paciente pode ser nulo
            $table->string('phone',20)->nullable(); // telefone do paciente
            $table->string('address')->nullable(); // endereço do paciente
            $table->string('internment_reason')->nullable(); // motivo da internação
            $table->date('internment_date')->nullable(); // data da internação
            $table->time('internment_time')->nullable(); // hora da internação
            $table->string('internment_location')->nullable(); // local da internação
            $table->string('bed')->nullable(); // leito
            $table->string('diagnosis')->nullable(); // diagnostico
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
