<?php

namespace Database\Seeders;

use App\Models\KategoriPengeluaran;
use Illuminate\Database\Seeder;

class KategoriPengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Bahan Baku',
                'deskripsi' => 'Pengeluaran untuk bahan baku produksi',
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'nama' => 'Salary',
                'deskripsi' => 'Pengeluaran untuk gaji karyawan',
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'nama' => 'Kantor',
                'deskripsi' => 'Pengeluaran untuk kebutuhan kantor',
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'nama' => 'Lainnya',
                'deskripsi' => 'Pengeluaran lainnya',
                'is_active' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($kategoris as $kategori) {
            KategoriPengeluaran::firstOrCreate(
                ['nama' => $kategori['nama']],
                $kategori
            );
        }
    }
}
