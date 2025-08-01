<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminInstansiRole = Role::firstOrCreate(['name' => 'admin_instansi']);

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('Super@123'),
                'email_verified_at' => now()
            ]
        );
        $adminInstansi = User::firstOrCreate(
            ['email' => 'didik.ngr@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('Super@123'),
                'email_verified_at' => now()
            ]
        );
        $adminInstansi->assignRole($adminInstansiRole);
        $superAdmin->assignRole($superAdminRole);
    }
}
