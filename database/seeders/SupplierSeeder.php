<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'kode_supplier' => 'SUP-' . strtoupper(substr(md5('1'), 0, 8)),
                'nama_supplier' => 'UD Sumber Makmur',
                'jenis_barang_jasa' => 'Barang Elektronik',
                'no_telepon' => '0213456789',
                'email' => 'sales@sumbermakmur.com',
                'alamat' => 'Jl. Pademangan Timur No. 17',
                'kota' => 'Jakarta Utara',
                'npwp' => '01.111.222.3-444.000',
                'nama_bank' => 'Bank Central Asia',
                'no_rekening' => '1234567890',
                'atas_nama_rekening' => 'UD Sumber Makmur',
                'status' => true,
                'catatan' => 'Supplier utama untuk komponen elektronik',
            ],
            [
                'kode_supplier' => 'SUP-' . strtoupper(substr(md5('2'), 0, 8)),
                'nama_supplier' => 'CV Logistik Indonesia',
                'jenis_barang_jasa' => 'Jasa Pengiriman',
                'no_telepon' => '0217890123',
                'email' => 'info@logistikindonesia.co.id',
                'alamat' => 'Jl. Tol Cengkareng Km 5',
                'kota' => 'Tangerang',
                'npwp' => '02.222.333.4-555.000',
                'nama_bank' => 'Bank Mandiri',
                'no_rekening' => '1300087654321',
                'atas_nama_rekening' => 'CV Logistik Indonesia',
                'status' => true,
                'catatan' => null,
            ],
            [
                'kode_supplier' => 'SUP-' . strtoupper(substr(md5('3'), 0, 8)),
                'nama_supplier' => 'PT Jaya Steel',
                'jenis_barang_jasa' => 'Bahan Bangunan',
                'no_telepon' => '0247654321',
                'email' => 'sales@jayasteel.co.id',
                'alamat' => 'Jl. INDUSTRI RAYA KAV 10',
                'kota' => 'Semarang',
                'npwp' => '03.333.444.5-666.000',
                'nama_bank' => 'Bank BRI',
                'no_rekening' => '001201018765543',
                'atas_nama_rekening' => 'PT Jaya Steel',
                'status' => true,
                'catatan' => 'Supplier bahan bangunan berkualitas',
            ],
            [
                'kode_supplier' => 'SUP-' . strtoupper(substr(md5('4'), 0, 8)),
                'nama_supplier' => 'Toko Besi Maju',
                'jenis_barang_jasa' => 'Besi & Baja',
                'no_telepon' => '0315678901',
                'email' => null,
                'alamat' => 'Jl. Perak Barat No. 56',
                'kota' => 'Surabaya',
                'npwp' => null,
                'nama_bank' => 'Bank BTPN',
                'no_rekening' => '711234567',
                'atas_nama_rekening' => 'Hendra Wijaya',
                'status' => false,
                'catatan' => 'Tidak aktif的合作关系',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
