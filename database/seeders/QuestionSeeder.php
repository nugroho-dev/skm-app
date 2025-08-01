<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unsur;
use App\Models\Question;
use Illuminate\Support\Str;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $pertanyaanPerUnsur = [
            'Persyaratan' => [
                'Informasi tentang persyaratan pelayanan mudah diperoleh.',
                'Persyaratan pelayanan mudah dipenuhi.'
            ],
            'Prosedur' => [
                'Prosedur pelayanan mudah dipahami.',
                'Prosedur pelayanan tidak berbelit-belit.'
            ],
            'Waktu Pelayanan' => [
                'Waktu pelayanan sesuai dengan standar yang ditetapkan.',
                'Pelayanan diselesaikan tepat waktu.'
            ],
        ];

        foreach ($pertanyaanPerUnsur as $unsurName => $pertanyaanList) {
            $unsur = Unsur::where('name', $unsurName)->first();

            if (!$unsur) {
                $this->command->warn("Unsur '$unsurName' tidak ditemukan.");
                continue;
            }

            foreach ($pertanyaanList as $text) {
                Question::create([
                    'unsur_id' => $unsur->id,
                    'text' => $text,
                    'slug' => Str::slug($text),
                ]);
            }
        }

        $this->command->info('Seeder Question berhasil dijalankan.');
    }
}
