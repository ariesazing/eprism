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
        Schema::create('email_blacklists', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->unique();
            $table->string('reason_code', 50);
            $table->text('reason_details')->nullable();
            $table->foreignId('blocked_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('blocked_at');
            $table->timestamps();

            $table->index('reason_code', 'idx_email_blacklists_reason_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_blacklists', function (Blueprint $table): void {
            $table->dropIndex('idx_email_blacklists_reason_code');
            $table->dropConstrainedForeignId('blocked_by');
        });

        Schema::dropIfExists('email_blacklists');
    }
};
