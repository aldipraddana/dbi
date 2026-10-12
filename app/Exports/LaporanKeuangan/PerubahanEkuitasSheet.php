<?php

declare(strict_types=1);

namespace App\Exports\LaporanKeuangan;

use App\Enums\JenisLaporanEnum;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * Laporan Perubahan Ekuitas periode laporan.
 *
 * Modal awal, penambahan modal, dan prive/dividen masih memakai asumsi
 * sementara karena belum ada tabel sumber modal.
 */
class PerubahanEkuitasSheet extends BaseLaporanSheet
{
    protected function jenisLaporan(): JenisLaporanEnum
    {
        return JenisLaporanEnum::PerubahanEkuitas;
    }

    public function render(): void
    {
        $this->writeReportHeader();
        $this->writeTableHeader(['A' => 'Uraian', 'B' => 'Nilai (Rp)']);

        $row = self::FIRST_CONTENT_ROW;

        // TODO: saldo modal awal masih memakai kas kumulatif sebelum periode,
        // karena belum ada tabel modal/pemilik.
        $modalAwal = $this->data->saldoKasSebelumPeriode();
        $this->writeLabel($row, 'Saldo Modal Awal');
        $this->writeAmount($row, $modalAwal);
        $row++;

        // TODO: belum ada tabel sumber
        $penambahanModal = 0.0;
        $this->writeLabel($row, 'Penambahan Modal (Setoran Pemilik)');
        $this->writeAmount($row, $penambahanModal);
        $row++;

        $labaRugi = $this->data->labaBersih();
        $this->writeLabel($row, 'Laba / (Rugi) Periode');
        $this->writeAmount($row, $labaRugi);
        $row++;

        // TODO: belum ada tabel sumber
        $prive = 0.0;
        $this->writeLabel($row, 'Prive / Dividen');
        $this->writeAmount($row, $prive);
        $row++;

        $modalAkhir = $modalAwal + $penambahanModal + $labaRugi - $prive;
        $this->writeLabel($row, 'Saldo Modal Akhir', tebal: true);
        $this->writeAmount($row, $modalAkhir, tebal: true);
        $this->styleTotal($row);
        $row += 2;

        $this->writeNote(
            $row,
            'Catatan: laba/rugi periode diambil dari laporan Profit and Loss. '
            . 'Saldo modal awal memakai kas kumulatif sebelum tanggal mulai laporan sebagai pengganti '
            . 'modal pemilik, sedangkan penambahan modal dan prive/dividen masih 0 karena belum ada data sumber.',
        );

        $this->finish(orientasi: PageSetup::ORIENTATION_LANDSCAPE);
    }
}
