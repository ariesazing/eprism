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
        Schema::create('research_versions', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('research_id')
                ->constrained('researches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('submission_type_id')
                ->constrained('submission_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedInteger('version_number');

            $table->foreignId('parent_version_id')
                ->nullable()
                ->constrained('research_versions')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('status_id')
                ->constrained('research_statuses')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->boolean('is_current')->default(false);

            $table->foreignId('submitted_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamp('submitted_at');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique(
                ['research_id', 'submission_type_id', 'version_number'],
                'uk_research_versions_sequence'
            );
            $table->index(['research_id', 'is_current'], 'idx_research_versions_current');
            $table->index('submission_type_id', 'idx_research_versions_submission_type');
            $table->index('status_id', 'idx_research_versions_status');
            $table->index('submitted_by', 'idx_research_versions_submitted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_versions');
    }
};