<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Mpp;
use Illuminate\Support\Str;

class MppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = ['MPP Kota Magelang', 'Luar MPP Kota Magelang'];

        foreach ($groups as $group) {
            Mpp::create([
                'name' => $group,
                'slug' => Str::slug($group),
            ]);
        }
    }
}
