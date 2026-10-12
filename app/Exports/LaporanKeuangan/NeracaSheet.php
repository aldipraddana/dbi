<?php

declare(strict_types=1);

namespace App\Exports\LaporanKeuangan;

use App\Enums\JenisLaporanEnum;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * Neraca (posisi keuangan) per tanggal akhir laporan.
 *
 * Hanya Kas yang bisa dihitung dari data yang ada; pos lain ditampilkan 0
 * karena belum punya tabel sumber.
 */
class NeracaSheet extends BaseLaporanSheet
{
    protected function jenisLaporan(): JenisLaporanEnum
    {
        return JenisLaporanEnum::Neraca;
    }

    public function render(): void
    {
        $this->writeReportHeader();
        $this->writeTableHeader(['A' => 'Uraian', 'B' => 'Nilai (Rp)']);

        $kas = $this->data->kasAkhir();

        $row = self::FIRST_CONTENT_ROW;

        // ---------------------------------------------------------------- ASET
        $this->writeSection($row, 'ASET');
        $row++;

        $this->writeSection($row, 'Aset Lancar');
        $row++;

        $this->writeLabel($row, 'Kas');
        $this->writeAmount($row, $kas);
        $row++;

        // TODO: belum ada tabel sumber
        $this->writeLabel($row, 'Piutang Usaha');
        $this->writeAmount($row, 0.0);
        $row++;

        // TODO: belum ada tabel sumber
        $this->writeLabel($row, 'Persediaan Barang');
        $this->writeAmount($row, 0.0);
        $row++;

        $totalAsetLancar = $kas;
        $this->writeLabel($row, 'Total Aset Lancar', tebal: true);
        $this->writeAmount($row, $totalAsetLancar, tebal: true);
        $this->styleTotal($row);
        $row += 2;

        $this->writeSection($row, 'Aset Tetap');
        $row++;

        // TODO: belum ada tabel sumber
        $this->writeLabel($row, 'Tanah, Bangunan, dan Peralatan');
        $this->writeAmount($row, 0.0);
        $row++;

        $this->writeLabel($row, 'Total Aset Tetap', tebal: true);
        $this->writeAmount($row, 0.0, tebal: true);
        $this->styleTotal($row);
        $row++;

        $totalAset = $totalAsetLancar;
        $this->writeLabel($row, 'Total Aset', tebal: true);
        $this->writeAmount($row, $totalAset, tebal: true);
        $this->styleTotal($row);
        $row += 2;

        // ---------------------------------------------------------- LIABILITAS
        $this->writeSection($row, 'LIABILITAS');
        $row++;

        // TODO: belum ada tabel sumber
        $this->writeLabel($row, 'Liabilitas Jangka Pendek');
        $this->writeAmount($row, 0.0);
        $row++;

        // TODO: belum ada tabel sumber
        $this->writeLabel($row, 'Liabilitas Jangka Panjang');
        $this->writeAmount($row, 0.0);
        $row++;

        $totalLiabilitas = 0.0;
        $this->writeLabel($row, 'Total Liabilitas', tebal: true);
        $this->writeAmount($row, $totalLiabilitas, tebal: true);
        $this->styleTotal($row);
        $row += 2;

        // -------------------------------------------------------------- EKUITAS
        $this->writeSection($row, 'EKUITAS');
        $row++;

        // TODO: belum ada tabel sumber
        $this->writeLabel($row, 'Modal Disetor');
        $this->writeAmount($row, 0.0);
        $row++;

        $labaDitahan = $totalAset - $totalLiabilitas;
        $this->writeLabel($row, 'Laba Ditahan (kumulatif)');
        $this->writeAmount($row, $labaDitahan);
        $row++;

        $totalEkuitas = $labaDitahan;
        $this->writeLabel($row, 'Total Ekuitas', tebal: true);
        $this->writeAmount($row, $totalEkuitas, tebal: true);
        $this->styleTotal($row);
        $row++;

        $this->writeLabel($row, 'Total Liabilitas dan Ekuitas', tebal: true);
        $this->writeAmount($row, $totalLiabilitas + $totalEkuitas, tebal: true);
        $this->styleTotal($row);
        $row += 2;

        $seimbang = $totalAset == ($totalLiabilitas + $totalEkuitas);
        $this->writeLabel($row, 'Pengecekan Aset = Liabilitas + Ekuitas');
        $this->writeLabel($row + 1, $seimbang ? 'OK' : 'TIDAK SEIMBANG', tebal: true);
        $row += 2;

        $this->writeNote(
            $row,
            'Catatan: neraca ini disusun dari saldo kas (Aset Lancar); piutang, persediaan, aset tetap, '
            . 'liabilitas, dan modal disetor belum memiliki tabel sumber sehingga ditampilkan 0. '
            . 'Ekuitas (Laba Ditahan) dihitung otomatis sebagai penyeimbang, sehingga balance '
            . 'Aset = Liabilitas + Ekuitas selalu terpenuhi.',
        );

        $this->finish(orientasi: PageSetup::ORIENTATION_PORTRAIT);
    }
}
