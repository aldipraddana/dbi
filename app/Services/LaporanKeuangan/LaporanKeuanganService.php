<?php

declare(strict_types=1);

namespace App\Services\LaporanKeuangan;

use App\Enums\JenisLaporanEnum;
use Carbon\Carbon;

/**
 * Penyusun payload laporan untuk halaman print.
 *
 * Memakai `LaporanSheetFactory` + `LaporanKeuanganDataService` yang sama dengan
 * export Excel, jadi urutan baris, label, subtotal, dan angka print dihitung oleh
 * logika yang sama dengan yang ditulis ke .xlsx.
 */
class LaporanKeuanganService
{
    /**
     * @return array<int, array{
     *     jenis: string,
     *     sheet: string,
     *     nama_perusahaan: string,
     *     judul: string,
     *     periode: string,
     *     orientasi: string,
     *     kolom: int,
     *     tabel: array<int, array{header: array<int, string>, baris: array<int, array<string, mixed>>}>
     * }>
     */
    public function buildLaporan(JenisLaporanEnum $jenis, Carbon $mulai, Carbon $akhir): array
    {
        Carbon::setLocale('id');

        $data = new LaporanKeuanganDataService($mulai, $akhir);
        $laporan = [];

        foreach (LaporanSheetFactory::jenisDipilih($jenis) as $jenisLaporan) {
            $writer = new HtmlSheetWriter;

            LaporanSheetFactory::buat($jenisLaporan, $writer, $mulai, $akhir, $data)->render();

            $header = $writer->header();

            $laporan[] = [
                'jenis' => $jenisLaporan->value,
                'sheet' => $jenisLaporan->sheetName(),
                'nama_perusahaan' => $header['namaPerusahaan'],
                'judul' => $header['judul'],
                'periode' => $header['periode'],
                'orientasi' => $writer->orientasi(),
                'kolom' => $writer->kolom(),
                'tabel' => $writer->tabel(),
            ];
        }

        return $laporan;
    }
}
