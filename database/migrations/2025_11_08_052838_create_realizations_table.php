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
        Schema::create('realizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->nullable()->constrained('proposals')->nullOnDelete();
            $table->foreignId('budget_item_id')->nullable()->constrained('annual_budget_items')->nullOnDelete();
            $table->foreignId('bendahara_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal');
            $table->decimal('nilai_realisasi', 18, 2)->default(0);
            $table->string('keterangan', 255)->nullable();
            $table->string('bukti_url', 255)->nullable();
            $table->timestamps();
            $table->index(['tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realizations');
    }
};
