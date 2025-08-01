<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Unsur;

class UnsurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unsurs = [
            'Persyaratan',
            'Prosedur',
            'Waktu Pelayanan',
            'Biaya/Tarif',
            'Produk Spesifikasi Jenis Pelayanan',
            'Kompetensi Pelaksana',
            'Perilaku Pelaksana',
            'Sarana dan Prasarana',
            'Penanganan Pengaduan, Saran, dan Masukan',
        ];

        foreach ($unsurs as $index => $name) {
            Unsur::create([
                'name' => $name,
                'slug' => Str::slug($name),
               
            ]);
        }
    }
}
