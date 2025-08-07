<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Occupation;
use Illuminate\Support\Str;

class OccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occupations = [
            'Tidak Bekerja',
            'Pegawai Negeri',
            'Pegawai Swasta',
            'Wiraswasta',
            'Petani/Peternak',
            'TNI',
            'Polri',
        ];

        foreach ($occupations as $name) {
             Occupation::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'type' => $name,
                    'slug' => Str::slug($name),
                ]
            );
        }
    }
}
