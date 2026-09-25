<?php

namespace App\Services\LaporanKeuangan;

use App\Enums\JenisLaporanEnum;
use Carbon\Carbon;

class LaporanKeuanganService
{
    public function getHeaderData(JenisLaporanEnum $jenisLaporan, Carbon $tanggalMulai, Carbon $tanggalAkhir): array
    {
        return [
            'namaPerusahaan' => config('app.name'),
            'namaLaporan' => $jenisLaporan->label(),
            'tanggalMulaiFormatted' => $tanggalMulai->translatedFormat('d F Y'),
            'tanggalAkhirFormatted' => $tanggalAkhir->translatedFormat('d F Y'),
            'tanggalMulai' => $tanggalMulai->format('Y-m-d'),
            'tanggalAkhir' => $tanggalAkhir->format('Y-m-d'),
            'jenisValue' => $jenisLaporan->value,
        ];
    }
}
