<?php

namespace Database\Seeders;

use App\Models\UserStatus;
use Illuminate\Database\Seeder;

class UserStatusSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        UserStatus::query()->upsert([
            [
                'status_name' => 'Pending Approval',
                'description' => 'Awaiting administrator approval',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'Active',
                'description' => 'Allowed to access the system',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'Inactive',
                'description' => 'Account temporarily inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'Suspended',
                'description' => 'Account suspended',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['status_name'], ['description', 'updated_at']);
    }
}
