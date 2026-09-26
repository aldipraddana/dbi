<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'kode_client' => 'CLT-' . strtoupper(substr(md5('1'), 0, 8)),
                'nama_client' => 'PT Maju Jaya Abadi',
                'jenis_client' => 'Perusahaan',
                'no_telepon' => '0215678901',
                'email' => 'info@majujaya.co.id',
                'alamat' => 'Jl. Sudirman No. 123',
                'kota' => 'Jakarta Selatan',
                'npwp' => '01.234.567.8-123.000',
                'status' => true,
                'catatan' => 'Klien tetap untuk proyek digital marketing',
            ],
            [
                'kode_client' => 'CLT-' . strtoupper(substr(md5('2'), 0, 8)),
                'nama_client' => 'Budi Santoso',
                'jenis_client' => 'Perorangan',
                'no_telepon' => '081234567890',
                'email' => 'budi.santoso@email.com',
                'alamat' => 'Jl. Merdeka No. 45',
                'kota' => 'Bandung',
                'npwp' => null,
                'status' => true,
                'catatan' => 'Konsumen individu',
            ],
            [
                'kode_client' => 'CLT-' . strtoupper(substr(md5('3'), 0, 8)),
                'nama_client' => 'CV Berkah Bersama',
                'jenis_client' => 'Perusahaan',
                'no_telepon' => '0221234567',
                'email' => 'contact@berkahbersama.id',
                'alamat' => 'Jl. Asia Afrika No. 88',
                'kota' => 'Bandung',
                'npwp' => '02.345.678.9-234.000',
                'status' => true,
                'catatan' => null,
            ],
            [
                'kode_client' => 'CLT-' . strtoupper(substr(md5('4'), 0, 8)),
                'nama_client' => 'PT Teknologi Nusantara',
                'jenis_client' => 'Perusahaan',
                'no_telepon' => '0219876543',
                'email' => 'hello@teknologi.co.id',
                'alamat' => 'Jl. Gatot Subroto Kav. 50',
                'kota' => 'Jakarta Pusat',
                'npwp' => '03.456.789.0-345.000',
                'status' => true,
                'catatan' => 'Startup teknologi',
            ],
            [
                'kode_client' => 'CLT-' . strtoupper(substr(md5('5'), 0, 8)),
                'nama_client' => 'Siti Aminah',
                'jenis_client' => 'Perorangan',
                'no_telepon' => '085678901234',
                'email' => 'siti.aminah@email.com',
                'alamat' => 'Jl._WR Supratman No. 12',
                'kota' => 'Surabaya',
                'npwp' => null,
                'status' => false,
                'catatan' => 'Tidak aktif sejak 2024',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
