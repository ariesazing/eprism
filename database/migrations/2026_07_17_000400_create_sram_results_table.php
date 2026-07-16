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
        Schema::create('sram_results', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('research_version_id')
                ->unique()
                ->constrained('research_versions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->decimal('overall_score', 5, 2)->nullable();
            $table->enum('overall_result', ['Passed', 'Failed']);
            $table->text('recommendation')->nullable();
            $table->timestamp('evaluated_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sram_results');
    }
};