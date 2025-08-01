<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Education;
use Illuminate\Support\Str;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educations = [
            'Tidak Sekolah',
            'SD/Sederajat',
            'SMP/Sederajat',
            'SMA/SMK Sederajat',
            'Diploma',
            'Sarjana (S1)',
            'Magister (S2)',
            'Doktor (S3)',
        ];

        foreach ($educations as $name) {
             Education::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'level' => $name,
                    'slug' => Str::slug($name),
                ]
            );
        }
    }
}
