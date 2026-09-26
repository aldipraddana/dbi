<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\Karyawan;
use Illuminate\Database\Seeder;

class KendaraanSeeder extends Seeder
{
    public function run(): void
    {
        $karyawan1 = Karyawan::where('nik', '3201234567890003')->first();
        $karyawan3 = Karyawan::where('nik', '3201234567890006')->first();

        $kendaraans = [
            [
                'no_polisi' => 'B 1234 ABC',
                'jenis_kendaraan' => 'Motor',
                'merk' => 'Honda',
                'model_tipe' => 'Beat CBS ISS',
                'tahun_pembuatan' => 2022,
                'warna' => 'Hitam',
                'no_rangka' => 'MH1JBFC2NCK12345',
                'no_mesin' => 'JC52E1301234',
                'kapasitas' => '1 Orang',
                'status_kepemilikan' => 'Milik Sendiri',
                'tanggal_berlaku_stnk' => '2025-02-28',
                'tanggal_berlaku_pajak' => '2025-01-31',
                'kondisi' => 'Baik',
                'karyawan_id' => $karyawan1?->id,
                'catatan' => 'Kendaraan operasional untuk pengiriman',
            ],
            [
                'no_polisi' => 'B 5678 DEF',
                'jenis_kendaraan' => 'Mobil',
                'merk' => 'Toyota',
                'model_tipe' => 'Avanza Veloz 1.5',
                'tahun_pembuatan' => 2021,
                'warna' => 'Putih',
                'no_rangka' => 'MHFWV7BN5M1234567',
                'no_mesin' => '2NRV1234567',
                'kapasitas' => '7 Orang',
                'status_kepemilikan' => 'Milik Sendiri',
                'tanggal_berlaku_stnk' => '2025-06-15',
                'tanggal_berlaku_pajak' => '2025-06-30',
                'kondisi' => 'Baik',
                'karyawan_id' => null,
                'catatan' => 'Kendaraan company car untuk manajemen',
            ],
            [
                'no_polisi' => 'B 9012 GHI',
                'jenis_kendaraan' => 'Motor',
                'merk' => 'Yamaha',
                'model_tipe' => 'Nmax CBS',
                'tahun_pembuatan' => 2023,
                'warna' => 'Merah',
                'no_rangka' => 'MKRR2BD3ND789012',
                'no_mesin' => 'G3E-789012',
                'kapasitas' => '2 Orang',
                'status_kepemilikan' => 'Sewa',
                'tanggal_berlaku_stnk' => '2025-09-30',
                'tanggal_berlaku_pajak' => '2025-09-30',
                'kondisi' => 'Baik',
                'karyawan_id' => $karyawan3?->id,
                'catatan' => 'Sewa dari dealer MotorIndo selama 1 tahun',
            ],
            [
                'no_polisi' => 'B 3456 JKL',
                'jenis_kendaraan' => 'Truk',
                'merk' => 'Mitsubishi',
                'model_tipe' => 'L300 Pick Up',
                'tahun_pembuatan' => 2020,
                'warna' => 'Abu-abu',
                'no_rangka' => 'MK2NF6DD0L0123456',
                'no_mesin' => '4D56U61234567',
                'kapasitas' => '1500 kg',
                'status_kepemilikan' => 'Milik Sendiri',
                'tanggal_berlaku_stnk' => '2025-03-31',
                'tanggal_berlaku_pajak' => '2025-03-31',
                'kondisi' => 'Rusak Ringan',
                'karyawan_id' => null,
                'catatan' => 'Butuh perbaikan di bagian rem',
            ],
            [
                'no_polisi' => 'B 7890 MNO',
                'jenis_kendaraan' => 'Mobil',
                'merk' => 'Daihatsu',
                'model_tipe' => 'Sigra R 1.0',
                'tahun_pembuatan' => 2019,
                'warna' => 'Silver',
                'no_rangka' => 'MHYWV7CS5JA890123',
                'no_mesin' => '1KRV8901234',
                'kapasitas' => '7 Orang',
                'status_kepemilikan' => 'Milik Sendiri',
                'tanggal_berlaku_stnk' => null,
                'tanggal_berlaku_pajak' => null,
                'kondisi' => 'Baik',
                'karyawan_id' => null,
                'catatan' => 'STNK belum diperpanjang',
            ],
        ];

        foreach ($kendaraans as $kendaraan) {
            Kendaraan::create($kendaraan);
        }
    }
}
