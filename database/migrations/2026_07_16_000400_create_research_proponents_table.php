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
        Schema::create('research_proponents', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('research_id')
                ->constrained('researches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('suffix', 20)->nullable();

            $table->string('position_title', 150)->nullable();
            $table->string('organizational_unit_name')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number', 20)->nullable();

            $table->timestamps();

            $table->index('research_id', 'idx_research_proponents_research');
            $table->index(['last_name', 'first_name'], 'idx_research_proponents_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_proponents');
    }
};
