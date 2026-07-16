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
        Schema::table('research_proponents', function (Blueprint $table): void {
            $table->string('photo_path')->nullable()->after('contact_number');
            $table->string('photo_disk', 50)->nullable()->after('photo_path');
            $table->string('photo_filename')->nullable()->after('photo_disk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_proponents', function (Blueprint $table): void {
            $table->dropColumn(['photo_path', 'photo_disk', 'photo_filename']);
        });
    }
};
