<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $allowedTitles = User::positionTitles();
        $driver = DB::connection()->getDriverName();

        DB::table('users')
            ->whereNotIn('position_title', $allowedTitles)
            ->update(['position_title' => 'Teacher I']);

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $enumValues = implode(',', array_map(
            static fn (string $title): string => DB::getPdo()->quote($title),
            $allowedTitles
        ));

        DB::statement("ALTER TABLE users MODIFY position_title ENUM({$enumValues}) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('ALTER TABLE users MODIFY position_title VARCHAR(150) NOT NULL');
    }
};
