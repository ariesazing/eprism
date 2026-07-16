<?php

namespace Database\Seeders;

use App\Models\ResearchStatus;
use Illuminate\Database\Seeder;

class ResearchStatusSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\ResearchStatus::query()->upsert([
            [
                'status_name' => 'Draft',
                'description' => 'Research draft is awaiting completion before final submission',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'Submitted',
                'description' => 'Research has been successfully submitted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'Under SRAM Assessment',
                'description' => 'Research is undergoing submission readiness assessment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'Under Review',
                'description' => 'Research has been forwarded for reviewer evaluation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'Revision Required',
                'description' => 'Research requires revisions based on evaluation results',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'Approved',
                'description' => 'Research has received final approval',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'Archived',
                'description' => 'Research record has been archived',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['status_name'], ['description', 'updated_at']);
    }
}
