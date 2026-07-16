<?php

namespace Database\Factories;

use App\Models\SubmissionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubmissionType>
 */
class SubmissionTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<SubmissionType>
     */
    protected $model = SubmissionType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_name' => fake()->unique()->randomElement(['Proposal', 'Completed']).'-'.fake()->unique()->numerify('##'),
            'description' => fake()->optional()->sentence(),
        ];
    }
}