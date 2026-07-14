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
        Schema::table('users', function (Blueprint $table): void {
            $table->text('rejection_reason')->nullable()->after('last_login_at');
            $table->foreignId('rejected_by')->nullable()->after('rejection_reason')->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->index('rejected_by', 'idx_users_rejectedby');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('idx_users_rejectedby');
            $table->dropConstrainedForeignId('rejected_by');
            $table->dropColumn(['rejection_reason', 'rejected_at']);
        });
    }
};
