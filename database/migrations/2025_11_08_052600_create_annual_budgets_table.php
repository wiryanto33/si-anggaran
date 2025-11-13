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
        Schema::create('annual_budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun')->index();
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nomor_dokumen')->nullable()->unique();
            $table->decimal('total_rencana', 18, 2)->default(0);
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_budgets');
    }
};
