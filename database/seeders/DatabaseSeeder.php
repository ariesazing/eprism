<?php

namespace Database\Seeders;

use App\Models\OrganizationalUnit;
use App\Models\Role;
use App\Models\UserStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserStatusSeeder::class,
            OrganizationalUnitSeeder::class,
            \Database\Seeders\ResearchCategorySeeder::class,
            \Database\Seeders\ResearchStatusSeeder::class,
        ]);

        $adminRoleId = Role::query()->where('role_name', 'Administrator')->value('id');
        $activeStatusId = UserStatus::query()->where('status_name', 'Active')->value('id');
        $defaultUnitId = OrganizationalUnit::query()->value('id');

        if ($adminRoleId && $activeStatusId && $defaultUnitId) {
            $adminUser = User::query()
                ->where('email', 'admin@gmail.com')
                ->orWhere('deped_id', 'ADMIN-0001')
                ->first();

            if (! $adminUser) {
                $adminUser = new User();
            }

            $adminUser->fill([
                'role_id' => $adminRoleId,
                'organizational_unit_id' => $defaultUnitId,
                'deped_id' => 'ADMIN-0001',
                'first_name' => 'System',
                'middle_name' => null,
                'last_name' => 'Administrator',
                'suffix' => null,
                'email' => 'admin@gmail.com',
                'password' => 'password',
                'position_title' => 'School Principal I',
                'contact_number' => null,
                'status_id' => $activeStatusId,
                'approved_by' => null,
                'approved_at' => now(),
                'email_verified_at' => now(),
                'last_login_at' => null,
            ]);

            $adminUser->save();
        }
    }
}
