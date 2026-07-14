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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 50)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('user_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_name', 50)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('organizational_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_name');
            $table->string('unit_code', 30)->nullable()->unique();
            $table->string('unit_type', 50);
            $table->string('district', 100)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('organizational_unit_id')->constrained('organizational_units')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('deped_id', 30)->nullable()->unique();
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('suffix', 20)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('position_title', 150);
            $table->string('contact_number', 20)->nullable();
            $table->foreignId('status_id')->constrained('user_statuses')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('role_id', 'idx_users_role');
            $table->index('status_id', 'idx_users_status');
            $table->index('organizational_unit_id', 'idx_users_orgunit');
            $table->index('last_name', 'idx_users_lastname');
            $table->index('email', 'idx_users_email');
            $table->index('approved_by', 'idx_users_approvedby');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('organizational_units');
        Schema::dropIfExists('user_statuses');
        Schema::dropIfExists('roles');
    }
};
