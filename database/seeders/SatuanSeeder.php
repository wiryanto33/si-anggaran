<?php

namespace Database\Seeders;

use App\Models\Satuan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            [
                'nama' => 'Satkat Koarmada II',
                'kode' => 'SATKAT-KOARMADA-II',
                'aktif' => true,
            ],
            [
                'nama' => 'Satfib Koarmada II',
                'kode' => 'SATFIB-KOARMADA-II',
                'aktif' => true,
            ],
            [
                'nama' => 'Disharkap Koarmada II',
                'kode' => 'DISHARKAP-KOARMADA-II',
                'aktif' => true,
            ],
        ];

        foreach ($rows as $data) {
            Satuan::firstOrCreate([
                'kode' => $data['kode'],
            ], $data);
        }
    }
}
