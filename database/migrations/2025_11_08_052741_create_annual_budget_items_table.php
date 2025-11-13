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
        Schema::create('annual_budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annual_budget_id')->constrained('annual_budgets')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('sumber_proposal_id')->nullable()->constrained('proposals')->nullOnDelete();
            $table->string('uraian', 255);
            $table->decimal('qty', 12, 2)->default(1);
            $table->decimal('harga_satuan', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_budget_items');
    }
};
