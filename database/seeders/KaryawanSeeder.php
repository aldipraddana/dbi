<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $karyawans = [
            [
                'nik' => '3201234567890001',
                'nama_lengkap' => 'Ahmad Fauzi',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1990-05-15',
                'no_telepon' => '081234567801',
                'email' => 'ahmad.fauzi@company.com',
                'alamat' => 'Jl. Melati No. 10 RT 003 RW 005',
                'kota' => 'Jakarta Selatan',
                'jabatan' => 'Staff IT',
                'departemen' => 'Information Technology',
                'tanggal_masuk_kerja' => '2020-03-01',
                'status_kepegawaian' => 'Tetap',
                'status_aktif' => true,
                'no_ktp' => '3201234567890001',
                'dokumen_pendukung' => null,
            ],
            [
                'nik' => '3201234567890002',
                'nama_lengkap' => 'Dewi Lestari',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1992-08-22',
                'no_telepon' => '081234567802',
                'email' => 'dewi.lestari@company.com',
                'alamat' => 'Jl. Anggrek No. 25',
                'kota' => 'Bandung',
                'jabatan' => 'HRD Manager',
                'departemen' => 'Human Resources',
                'tanggal_masuk_kerja' => '2019-01-15',
                'status_kepegawaian' => 'Tetap',
                'status_aktif' => true,
                'no_ktp' => '3201234567890002',
                'dokumen_pendukung' => null,
            ],
            [
                'nik' => '3201234567890003',
                'nama_lengkap' => 'Rizky Ramadhan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1995-12-10',
                'no_telepon' => '081234567803',
                'email' => 'rizky.ramadhan@company.com',
                'alamat' => 'Jl. Mawar No. 8',
                'kota' => 'Bekasi',
                'jabatan' => 'Driver',
                'departemen' => 'Operasional',
                'tanggal_masuk_kerja' => '2022-06-01',
                'status_kepegawaian' => 'Kontrak',
                'status_aktif' => true,
                'no_ktp' => '3201234567890003',
                'dokumen_pendukung' => null,
            ],
            [
                'nik' => '3201234567890004',
                'nama_lengkap' => 'Putri Ayu Wulandari',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1998-03-28',
                'no_telepon' => '081234567804',
                'email' => 'putri.wulandari@company.com',
                'alamat' => 'Jl. Kenanga No. 15',
                'kota' => 'Depok',
                'jabatan' => 'Admin Keuangan',
                'departemen' => 'Finance',
                'tanggal_masuk_kerja' => '2023-02-01',
                'status_kepegawaian' => 'Tetap',
                'status_aktif' => true,
                'no_ktp' => '3201234567890004',
                'dokumen_pendukung' => null,
            ],
            [
                'nik' => '3201234567890005',
                'nama_lengkap' => 'Bagas Pratama',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2000-07-19',
                'no_telepon' => '081234567805',
                'email' => 'bagas.pratama@company.com',
                'alamat' => null,
                'kota' => 'Bogor',
                'jabatan' => 'Magang IT',
                'departemen' => 'Information Technology',
                'tanggal_masuk_kerja' => '2024-01-10',
                'status_kepegawaian' => 'Magang',
                'status_aktif' => true,
                'no_ktp' => '3201234567890005',
                'dokumen_pendukung' => null,
            ],
            [
                'nik' => '3201234567890006',
                'nama_lengkap' => 'Hendra Gunawan',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1988-11-05',
                'no_telepon' => null,
                'email' => null,
                'alamat' => 'Jl. Dahlia No. 33',
                'kota' => 'Tangerang',
                'jabatan' => 'Driver',
                'departemen' => 'Operasional',
                'tanggal_masuk_kerja' => '2021-09-15',
                'status_kepegawaian' => 'Tetap',
                'status_aktif' => false,
                'no_ktp' => '3201234567890006',
                'dokumen_pendukung' => null,
            ],
        ];

        foreach ($karyawans as $karyawan) {
            Karyawan::create($karyawan);
        }
    }
}
