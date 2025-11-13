<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proposal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('uraian', 255);
            $table->decimal('qty', 12, 2)->default(1);
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga_satuan', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->timestamps();
            $table->index(['proposal_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('proposal_items');
    }
};
