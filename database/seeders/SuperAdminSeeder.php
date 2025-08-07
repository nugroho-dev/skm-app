<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Institution;
use App\Models\InstitutionGroup;
use App\Models\Mpp;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mppMagelang = Mpp::where('slug', 'mpp-kota-magelang')->first();
        $groupMagelang = InstitutionGroup::where('slug', 'kota-magelang')->first();
        // Buat instansi
        $name = 'Dinas Penananam Modal dan Pelayanan Terpadu Satu Pintu';
        $institution = Institution::firstOrCreate(
            ['name' => $name,
            'slug' => Str::slug($name),
            'mpp_id' => $mppMagelang->id,
            'institution_group_id' => $groupMagelang->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Agus Makmur',
                'email_verified_at' => now(),
                'password' => Hash::make('Super1@123adminS'), // Ganti dengan password yang aman
                'role' => 'super_admin',
                'is_approved' => true,
                'institution_id' => $institution->id,
            
            ]
        );
        User::updateOrCreate(
            ['email' => 'superadmin2@example.com'],
            [
                'name' => 'Sonal Ravi Sadarangani',
                'email_verified_at' => now(),
                'password' => Hash::make('Super1@123adminS'), // Ganti dengan password yang aman
                'role' => 'super_admin',
                'is_approved' => true,
                'institution_id' => $institution->id,
            
            ]
        );
         User::updateOrCreate(
            ['email' => 'admin.instansi@gmail.com'],
            [
                'name' => 'Syaiful Zen',
                'email_verified_at' => now(),
                'password' => Hash::make('Intansi1@123admiN'), // Ganti dengan password yang aman
                'role' => 'super_admin',
                'is_approved' => true,
                'institution_id' => $institution->id,
            
            ]
        );
         User::updateOrCreate(
            ['email' => 'admin.instansi2@gmail.com'],
            [
                'name' => 'Tyty Chandra',
                'email_verified_at' => now(),
                'password' => Hash::make('Intansi2@123admiN'), // Ganti dengan password yang aman
                'role' => 'super_admin',
                'is_approved' => true,
                'institution_id' => $institution->id,
            
            ]
        );

    }
}
