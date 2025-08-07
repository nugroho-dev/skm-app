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
                'Bagaimana pendapat Saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya ?'
            ],
            'Sistem, Mekanisme, dan Prosedur' => [
                'Bagaimana pemahaman Saudara tentang kemudahan prosedur pelayanan di unit ini ?'
            ],
            'Waktu Penyelesaian' => [
                'Bagaimana pendapat Saudara tentang kecepatan waktu dalam memberikan pelayanan ?'
            ],
            'Biaya/Tarif'=> [
                'Bagaimana pendapat Saudara tentang kewajaran biaya / tarif dalam pelayanan ?'
            ],
            'Produk Spesifikasi Jenis Pelayanan'=> [
                'Bagaimana pendapat Saudara tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan ?'
            ],
            'Kompetensi Pelaksana'=> [
                'Bagaimana pendapat Saudara tentang kompetensi/kemampuan petugas dalam pelayanan ?'
            ],
            'Perilaku Pelaksana'=> [
                'Bagamana pendapat saudara perilaku petugas dalam pelayanan terkait kesopanan dan keramahan ?
'
            ],
            'Penanganan Pengaduan, Saran, dan Masukan'=> [
                'Bagaimana pendapat Saudara tentang kualitas sarana dan prasarana ?'
            ],
            'Sarana dan Prasarana'=> [
                'Bagaimana pendapat Saudara tentang penanganan pengaduan pengguna layanan ?'
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
