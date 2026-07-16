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
        Schema::create('sram_checks', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('sram_result_id')
                ->constrained('sram_results')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('check_type', 50);
            $table->decimal('score', 5, 2)->nullable();
            $table->enum('result', ['Passed', 'Failed']);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('sram_result_id', 'idx_sram_checks_result');
            $table->index('check_type', 'idx_sram_checks_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sram_checks');
    }
};