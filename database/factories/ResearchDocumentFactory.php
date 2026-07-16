<?php

namespace Database\Factories;

use App\Models\Research;
use App\Models\ResearchDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResearchDocument>
 */
class ResearchDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ResearchDocument>
     */
    protected $model = ResearchDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $storedName = fake()->uuid().'.pdf';

        return [
            'research_id' => Research::factory(),
            'document_type' => fake()->randomElement([
                'Proposal',
                'Ethics Form',
                'Budget Plan',
            ]),
            'original_filename' => fake()->words(2, true).'.pdf',
            'stored_filename' => $storedName,
            'file_path' => 'research/'.fake()->uuid().'/'.$storedName,
            'storage_disk' => 'public',
            'file_extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(1_024, 5_000_000),
            'uploaded_by' => User::factory(),
            'uploaded_at' => now(),
        ];
    }
}
