<?php

namespace Database\Factories;

use App\Models\Research;
use App\Models\ResearchStatus;
use App\Models\ResearchVersion;
use App\Models\SubmissionType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResearchVersion>
 */
class ResearchVersionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ResearchVersion>
     */
    protected $model = ResearchVersion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'research_id' => Research::factory(),
            'submission_type_id' => SubmissionType::factory(),
            'version_number' => 1,
            'parent_version_id' => null,
            'status_id' => ResearchStatus::factory(),
            'is_current' => true,
            'submitted_by' => User::factory(),
            'submitted_at' => now(),
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}