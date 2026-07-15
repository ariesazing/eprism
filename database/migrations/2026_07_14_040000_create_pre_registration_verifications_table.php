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
        Schema::create('pre_registration_verifications', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->unique();
            $table->string('token_hash', 64);
            $table->json('registration_payload');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('expires_at', 'idx_pre_reg_verifications_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pre_registration_verifications', function (Blueprint $table): void {
            $table->dropIndex('idx_pre_reg_verifications_expires_at');
        });

        Schema::dropIfExists('pre_registration_verifications');
    }
};
