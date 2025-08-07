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
                'name' => 'Agus Makmur',
                'password' => bcrypt('Super1@123adminS'),
                'email_verified_at' => now()
            ]

        );
        $superAdmin2 = User::firstOrCreate(
            ['email' => 'superadmin2@example.com'],
            [
                'name' => 'Sonal Ravi Sadarangani',
                'password' => bcrypt('Super2@123adminS'),
                'email_verified_at' => now()
            ]
            
        );
        $adminInstansi = User::firstOrCreate(
            ['email' => 'admin.instansi@gmail.com'],
            [
                'name' => 'Syaiful Zen',
                'password' => bcrypt('Intansi1@123admiN'),
                'email_verified_at' => now()
            ]
        );
        $adminInstansi2 = User::firstOrCreate(
            ['email' => 'admin.instansi2@gmail.com'],
            [
                'name' => 'Tyty Chandra',
                'password' => bcrypt('Intansi2@123admiN'),
                'email_verified_at' => now()
            ]
        );
        $adminInstansi->assignRole($adminInstansiRole);
        $adminInstansi2->assignRole($adminInstansiRole);
        $superAdmin->assignRole($superAdminRole);
        $superAdmin2->assignRole($superAdminRole);
    }
}
