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
        Schema::create('researches', function (Blueprint $table): void {
            $table->id();

            $table->string('research_code', 30)->unique();
            $table->string('title', 500);

            $table->foreignId('lead_proponent_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('organizational_unit_id')
                ->constrained('organizational_units')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('category_id')
                ->constrained('research_categories')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('status_id')
                ->constrained('research_statuses')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('lead_proponent_id', 'idx_researches_lead_proponent');
            $table->index('organizational_unit_id', 'idx_researches_org_unit');
            $table->index('category_id', 'idx_researches_category');
            $table->index('status_id', 'idx_researches_status');
            $table->index('submitted_at', 'idx_researches_submitted_at');
            $table->index('deleted_at', 'idx_researches_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('researches');
    }
};
