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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('time_context_id')->nullable()->constrained()->noActionOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained()->noActionOnDelete();
            $table->decimal('profit_margin');
            $table->decimal('base_price');
            $table->decimal('cost_price');
            $table->decimal('price_with_profit');
            $table->decimal('sale_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
