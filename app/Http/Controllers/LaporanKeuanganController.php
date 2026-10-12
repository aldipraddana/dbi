<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\JenisLaporanEnum;
use App\Exports\LaporanKeuangan\LaporanKeuanganExport;
use App\Http\Requests\DownloadLaporanKeuanganRequest;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanKeuanganController extends Controller
{
    /**
     * Download laporan keuangan dalam format Excel (.xlsx).
     */
    public function downloadExcel(DownloadLaporanKeuanganRequest $request): StreamedResponse
    {
        $validated = $request->validated();

        $export = new LaporanKeuanganExport(
            jenis: JenisLaporanEnum::from((string) $validated['jenis']),
            mulai: Carbon::parse((string) $validated['tanggal_mulai']),
            akhir: Carbon::parse((string) $validated['tanggal_akhir']),
        );

        return $export->download();
    }
}
