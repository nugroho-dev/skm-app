<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Institution;
use App\Models\InstitutionGroup;
use App\Models\Mpp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mppMagelang = Mpp::where('slug', 'mpp-kota-magelang')->first();
        $mppLuarMagelang = Mpp::firstOrCreate(
            ['slug' => 'luar-mpp-kota-magelang'],
            ['id' => Str::uuid(), 'name' => 'Luar MPP Kota Magelang']
        );

        $groupMagelang = InstitutionGroup::where('slug', 'kota-magelang')->first();
        $groupLuarMagelang = InstitutionGroup::where('slug', 'luar-kota-magelang')->first();

        if (!$mppMagelang || !$groupMagelang || !$groupLuarMagelang) {
            $this->command->error('Seeder gagal: pastikan seeder untuk Mpp dan InstitutionGroup sudah dijalankan.');
            return;
        }

        // 1. Instansi bergabung dengan MPP & instansi induk Kota Magelang
        $data1 = [
            'Dinas Kependudukan Kota Magelang',
            'Dinas Pendidikan Kota Magelang',
        ];
        foreach ($data1 as $name) {
            Institution::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'mpp_id' => $mppMagelang->id,
                'institution_group_id' => $groupMagelang->id,
            ]);
        }

        // 2. Instansi bergabung dengan MPP & instansi induk dari luar Kota Magelang
        $data2 = [
            'Imigrasi Wilayah Kedu',
            'Kementerian Hukum dan HAM RI',
        ];
        foreach ($data2 as $name) {
            Institution::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'mpp_id' => $mppMagelang->id,
                'institution_group_id' => $groupLuarMagelang->id,
            ]);
        }

        // 3. Instansi TIDAK bergabung dengan MPP Kota Magelang,
        //    tapi instansi induk Kota Magelang → mpp_id diisi dengan MPP luar
        $data3 = [
            'Dinas Arsip dan Perpustakaan Kota Magelang',
            'Dinas Ketahanan Pangan Kota Magelang',
        ];
        foreach ($data3 as $name) {
            Institution::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'mpp_id' => $mppLuarMagelang->id,
                'institution_group_id' => $groupMagelang->id,
            ]);
        }
    }
}
