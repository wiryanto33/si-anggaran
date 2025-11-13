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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('satuan_id')->nullable()->after('id')->constrained('satuans')->nullOnDelete();
            $table->boolean('active')->default(false)->after('remember_token');
            $table->timestamp('last_login_at')->nullable()->after('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('satuan_id');
            $table->dropColumn(['active', 'last_login_at']);
        });
    }
};
