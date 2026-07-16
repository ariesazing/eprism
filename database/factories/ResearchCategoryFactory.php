<?php

namespace Database\Factories;

use App\Models\ResearchCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResearchCategory>
 */
class ResearchCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ResearchCategory>
     */
    protected $model = ResearchCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
