<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('perencana_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('kode_usulan')->unique();
            $table->string('judul', 255);
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('tahun')->index();
            $table->date('tanggal_pengajuan')->nullable();
            $table->enum('status', ['draft', 'diajukan', 'diverifikasi', 'disetujui', 'ditolak'])->default('draft')->index();
            $table->text('catatan_verifikator')->nullable();
            $table->text('catatan_pimpinan')->nullable();
            $table->decimal('total_rencana', 18, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['satuan_id', 'tahun']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
