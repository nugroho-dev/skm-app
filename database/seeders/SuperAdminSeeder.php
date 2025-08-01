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
                'name' => 'Super Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('SuperSecure123!'), // Ganti dengan password yang aman
                'role' => 'super_admin',
                'is_approved' => true,
                'institution_id' => $institution->id,
            
            ]
        );
    }
}
