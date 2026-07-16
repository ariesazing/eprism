<?php

namespace Database\Seeders;

use App\Models\ResearchCategory;
use Illuminate\Database\Seeder;

class ResearchCategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        ResearchCategory::query()->upsert([
            [
                'category_name' => 'Action Research',
                'description' => 'Research focused on improving classroom or institutional practices',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Basic Research',
                'description' => 'Research focused on generating new knowledge and understanding',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['category_name'], ['description', 'updated_at']);
    }
}
