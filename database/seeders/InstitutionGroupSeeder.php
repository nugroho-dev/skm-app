<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InstitutionGroup;
use Illuminate\Support\Str;

class InstitutionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = ['Kota Magelang', 'Luar Kota Magelang'];

        foreach ($groups as $group) {
            InstitutionGroup::create([
                'name' => $group,
                'slug' => Str::slug($group),
                
            ]);
        }
    }
}
