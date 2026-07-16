<?php

namespace Database\Seeders;

use App\Models\SubmissionType;
use Illuminate\Database\Seeder;

class SubmissionTypeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        SubmissionType::query()->upsert([
            [
                'type_name' => 'Proposal',
                'description' => 'Submission for the proposal stage of a research lifecycle.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_name' => 'Completed',
                'description' => 'Submission for the completed research stage of a research lifecycle.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['type_name'], ['description', 'updated_at']);
    }
}