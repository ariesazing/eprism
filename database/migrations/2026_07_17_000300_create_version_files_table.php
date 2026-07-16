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
        Schema::create('version_files', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('research_version_id')
                ->constrained('research_versions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('document_name', 100);
            $table->string('original_file_name');
            $table->string('stored_file_name')->unique();
            $table->string('file_path', 500);
            $table->string('file_type', 100);
            $table->unsignedBigInteger('file_size');

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamp('uploaded_at')->useCurrent();

            $table->timestamps();

            $table->index('research_version_id', 'idx_version_files_research_version');
            $table->index('uploaded_by', 'idx_version_files_uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('version_files');
    }
};