<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::query()->upsert([
            [
                'role_name' => 'Administrator',
                'description' => 'System administrator with full access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'Reviewer',
                'description' => 'Research evaluator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'Proponent',
                'description' => 'Research proponent',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['role_name'], ['description', 'updated_at']);
    }
}
