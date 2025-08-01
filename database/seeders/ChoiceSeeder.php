<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Choice;
use Illuminate\Support\Str;

class ChoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $questions = Question::all();

        if ($questions->isEmpty()) {
            $this->command->warn('Seeder Answer dilewati: Tidak ada data response atau question.');
            return;
        }

        foreach ($questions as $question) {
            Choice::insert([
                [
                    'id' => Str::uuid(),
                    'question_id' => $question->id,
                    'label' => 'Sangat Baik',
                    'score' => 4,
                ],
                [
                    'id' => Str::uuid(),
                    'question_id' => $question->id,
                    'label' => 'Baik',
                    'score' => 3,
                ],
                [
                    'id' => Str::uuid(),
                    'question_id' => $question->id,
                    'label' => 'Cukup',
                    'score' => 2,
                ],
                [
                    'id' => Str::uuid(),
                    'question_id' => $question->id,
                    'label' => 'Kurang',
                    'score' => 1,
                ],
            ]);
        }
       

        $this->command->info('Seeder Choise berhasil dijalankan.');
    }
}
