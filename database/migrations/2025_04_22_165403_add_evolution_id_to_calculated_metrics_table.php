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
        Schema::table('calculated_metrics', function (Blueprint $table) {
            $table->foreignId('evolution_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calculated_metrics', function (Blueprint $table) {
            $table->dropForeign(['evolution_id']);
            $table->dropColumn('evolution_id');
        });
    }
};
