<?php

namespace Database\Factories;

use App\Models\OrganizationalUnit;
use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\ResearchStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Research>
 */
class ResearchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Research>
     */
    protected $model = Research::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $organizationalUnitId = OrganizationalUnit::query()->firstOrCreate(
            ['unit_code' => 'DEFAULT-OU'],
            [
                'unit_name' => 'Default Organizational Unit',
                'unit_type' => 'School',
                'district' => null,
                'address' => null,
            ]
        )->id;

        return [
            'research_code' => 'RSH-'.fake()->unique()->numerify('########'),
            'title' => fake()->sentence(8),
            'lead_proponent_id' => User::factory(),
            'organizational_unit_id' => $organizationalUnitId,
            'category_id' => ResearchCategory::factory(),
            'status_id' => ResearchStatus::factory(),
            'submitted_at' => null,
            'approved_at' => null,
            'archived_at' => null,
        ];
    }
}
