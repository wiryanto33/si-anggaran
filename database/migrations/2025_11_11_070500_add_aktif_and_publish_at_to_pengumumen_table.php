<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumumen', function (Blueprint $table) {
            $table->boolean('aktif')->default(true)->after('file');
            $table->timestamp('publish_at')->nullable()->after('aktif');
        });
    }

    public function down(): void
    {
        Schema::table('pengumumen', function (Blueprint $table) {
            $table->dropColumn(['aktif', 'publish_at']);
        });
    }
};

