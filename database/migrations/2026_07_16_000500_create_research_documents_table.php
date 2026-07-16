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
        Schema::create('research_documents', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('research_id')
                ->constrained('researches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('document_type', 100);
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path', 500);
            $table->string('storage_disk', 50)->default('public');
            $table->string('file_extension', 20);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamp('uploaded_at')->useCurrent();

            $table->timestamps();
            $table->softDeletes();

            $table->index('research_id', 'idx_research_documents_research');
            $table->index('document_type', 'idx_research_documents_type');
            $table->index('uploaded_by', 'idx_research_documents_uploaded_by');
            $table->index('storage_disk', 'idx_research_documents_storage_disk');
            $table->index('deleted_at', 'idx_research_documents_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_documents');
    }
};
