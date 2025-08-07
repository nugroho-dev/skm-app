<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Institution;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Contoh layanan per instansi berdasarkan nama
        $data = [
            'Dinas Kependudukan dan Pencatatan Sipil' => [
                'Pembuatan KTP',
                'Pembuatan KK',
                'Pembuatan Akta Kelahiran'
            ],
            'Dinas Sosial Kota Magelang' => [
                'Bantuan Sosial Tunai',
                'Layanan PKH'
            ],
            'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu' => [
                'Izin Usaha Mikro',
                'Izin Mendirikan Bangunan'
            ],
        ];

        foreach ($data as $institutionName => $services) {
            $institution = Institution::where('name', $institutionName)->first();

            if (!$institution) {
                $this->command->warn("Institusi '$institutionName' tidak ditemukan.");
                continue;
            }

            foreach ($services as $serviceName) {
                Service::create([
                    'institution_id' => $institution->id,
                    'name' => $serviceName,
                    'slug' => Str::slug($serviceName),
                ]);
            }
        }

        $this->command->info('Seeder untuk layanan (services) berhasil dijalankan.');
    }
}
