<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('approved_id', 6)->nullable()->unique()->after('rejection_reason');
            $table->timestamp('archived_at')->nullable()->after('approved_id');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['approved_id', 'archived_at']);
        });
    }
};
