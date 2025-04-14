<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Essa tabela eh pra cadastrar as opções de cada grupo do corpo
     * Ex: cadastrar a opção "Nariz com corrimento de sangue" para o grupo Nariz
     */
    public function up(): void
    {
        Schema::create('assessment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_group_id')->constrained()->onDelete('cascade');
            $table->string('description');// descrição
            $table->enum('severity', ['None', 'Low', 'Medium', 'High', 'Critical'])->default('None');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_options');
    }
};
