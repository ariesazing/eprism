<?php

namespace Database\Factories;

use App\Models\ResearchVersion;
use App\Models\User;
use App\Models\VersionFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VersionFile>
 */
class VersionFileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<VersionFile>
     */
    protected $model = VersionFile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $storedName = fake()->uuid().'.pdf';

        return [
            'research_version_id' => ResearchVersion::factory(),
            'document_name' => fake()->randomElement([
                'Chapter I',
                'Chapter II',
                'References',
                'Attachments',
            ]),
            'original_file_name' => fake()->words(2, true).'.pdf',
            'stored_file_name' => $storedName,
            'file_path' => 'research_versions/'.fake()->uuid().'/'.$storedName,
            'file_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(1_024, 5_000_000),
            'uploaded_by' => User::factory(),
            'uploaded_at' => now(),
        ];
    }
}