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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('actor_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('aksi', ['diajukan', 'diverifikasi', 'disetujui', 'ditolak', 'revisi']);
            $table->text('catatan')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
            $table->index(['proposal_id', 'acted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
