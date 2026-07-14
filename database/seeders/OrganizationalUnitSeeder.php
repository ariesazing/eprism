<?php

namespace Database\Seeders;

use App\Models\OrganizationalUnit;
use Illuminate\Database\Seeder;

class OrganizationalUnitSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        OrganizationalUnit::query()->upsert([
            [
                'unit_name' => 'Santiago National High School',
                'unit_code' => '300599',
                'unit_type' => 'Senior High School',
                'district' => 'District I',
                'address' => 'Santiago City, Isabela',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'unit_name' => 'DepEd Schools Division Office Santiago City',
                'unit_code' => 'SDO-SC',
                'unit_type' => 'Schools Division Office',
                'district' => null,
                'address' => "Children's Park, Calaocan, Santiago City",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['unit_code'], ['unit_name', 'unit_type', 'district', 'address', 'updated_at']);
    }
}
