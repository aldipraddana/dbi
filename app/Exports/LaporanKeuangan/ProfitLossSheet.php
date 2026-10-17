<?php

declare(strict_types=1);

namespace App\Exports\LaporanKeuangan;

use App\Enums\JenisLaporanEnum;
use App\Enums\KategoriGaji;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * Profit and Loss periode laporan (basis kas).
 */
class ProfitLossSheet extends BaseLaporanSheet
{
    protected function jenisLaporan(): JenisLaporanEnum
    {
        return JenisLaporanEnum::ProfitLoss;
    }

    public function render(): void
    {
        $this->writeReportHeader();
        $this->writeTableHeader([
            'A' => 'Uraian',
            'B' => 'Nilai (Rp)',
            'C' => '% terhadap Pendapatan',
        ]);

        $row = self::FIRST_CONTENT_ROW;

        // ------------------------------------------------------------ PENDAPATAN
        $this->writeSection($row, 'PENDAPATAN', self::KOLOM_PERSENTASE);
        $row++;

        foreach ($this->data->pendapatanPerTipePembayaran() as $label => $nilai) {
            $this->writeLabel($row, $label);
            $this->writeAmount($row, $nilai);
            $this->writePercent($row, $this->rasioKePendapatan($nilai));
            $row++;
        }

        $pendapatan = $this->data->pendapatan();
        $this->writeLabel($row, 'Total Pendapatan', tebal: true);
        $this->writeAmount($row, $pendapatan, tebal: true);
        $this->writePercent($row, $this->rasioKePendapatan($pendapatan));
        $this->styleTotal($row, self::KOLOM_PERSENTASE);
        $row += 2;

        // ------------------------------------------------------------------- HPP
        $this->writeSection($row, 'HARGA POKOK PENJUALAN (HPP)', self::KOLOM_PERSENTASE);
        $row++;

        $hpp = $this->data->hpp();
        $this->writeLabel($row, 'Bahan Baku');
        $this->writeAmount($row, $hpp);
        $this->writePercent($row, $this->rasioKePendapatan($hpp));
        $row++;

        $this->writeLabel($row, 'Total HPP', tebal: true);
        $this->writeAmount($row, $hpp, tebal: true);
        $this->writePercent($row, $this->rasioKePendapatan($hpp));
        $this->styleTotal($row, self::KOLOM_PERSENTASE);
        $row++;

        $labaKotor = $this->data->labaKotor();
        $this->writeLabel($row, 'Laba Kotor', tebal: true);
        $this->writeAmount($row, $labaKotor, tebal: true);
        $this->writePercent($row, $this->rasioKePendapatan($labaKotor));
        $this->styleTotal($row, self::KOLOM_PERSENTASE);
        $row += 2;

        // --------------------------------------------------- BEBAN OPERASIONAL
        $this->writeSection($row, 'BEBAN OPERASIONAL', self::KOLOM_PERSENTASE);
        $row++;

        foreach ($this->data->bebanOperasionalPerJenis() as $jenis => $nilai) {
            $label = match ($jenis) {
                'operasional' => 'Operasional',
                'gaji' => 'Gaji',
                'biaya_kantor' => 'Biaya Kantor',
                default => ucfirst($jenis),
            };

            $this->writeLabel($row, $label);
            $this->writeAmount($row, $nilai);
            $this->writePercent($row, $this->rasioKePendapatan($nilai));
            $row++;

            if ($jenis === 'gaji') {
                foreach ($this->data->gajiPerKategori() as $kategori => $nilaiKategori) {
                    $labelKategori = KategoriGaji::tryFrom($kategori)?->label() ?? ucfirst($kategori);

                    $this->writeLabel($row, '   Gaji: ' . $labelKategori);
                    $this->writeAmount($row, $nilaiKategori);
                    $this->writePercent($row, $this->rasioKePendapatan($nilaiKategori));
                    $row++;
                }
            }
        }

        $bebanOperasional = $this->data->bebanOperasional();
        $this->writeLabel($row, 'Total Beban Operasional', tebal: true);
        $this->writeAmount($row, $bebanOperasional, tebal: true);
        $this->writePercent($row, $this->rasioKePendapatan($bebanOperasional));
        $this->styleTotal($row, self::KOLOM_PERSENTASE);
        $row++;

        $labaOperasional = $this->data->labaOperasional();
        $this->writeLabel($row, 'Laba Operasional', tebal: true);
        $this->writeAmount($row, $labaOperasional, tebal: true);
        $this->writePercent($row, $this->rasioKePendapatan($labaOperasional));
        $this->styleTotal($row, self::KOLOM_PERSENTASE);
        $row += 2;

        // ------------------------------------------- PENDAPATAN / BEBAN LAIN
        $this->writeSection($row, 'BEBAN LAIN', self::KOLOM_PERSENTASE);
        $row++;

        $bebanLain = $this->data->bebanLain();
        $this->writeLabel($row, 'Biaya Lain-Lain');
        $this->writeAmount($row, -$bebanLain);
        $this->writePercent($row, $this->rasioKePendapatan(-$bebanLain));
        $row++;

        $this->writeLabel($row, 'Total Beban Lain', tebal: true);
        $this->writeAmount($row, -$bebanLain, tebal: true);
        $this->writePercent($row, $this->rasioKePendapatan(-$bebanLain));
        $this->styleTotal($row, self::KOLOM_PERSENTASE);
        $row += 2;

        $labaBersih = $this->data->labaBersih();
        $this->writeLabel($row, 'Laba Bersih', tebal: true);
        $this->writeAmount($row, $labaBersih, tebal: true);
        $this->writePercent($row, $this->rasioKePendapatan($labaBersih));
        $this->styleTotal($row, self::KOLOM_PERSENTASE);
        $row += 2;

        // TODO: konfirmasi apakah gaji proyek masuk HPP
        $this->writeNote(
            $row,
            'Catatan: seluruh angka berbasis kas. HPP diambil dari jenis "bahan_baku"; '
            . 'beban operasional diambil dari jenis "operasional", "gaji", dan "biaya_kantor"; '
            . 'pendapatan/beban lain diambil dari jenis "biaya_lain".',
            self::KOLOM_PERSENTASE,
        );

        $this->finish(
            kolomNilai: [self::KOLOM_NILAI, self::KOLOM_PERSENTASE],
            orientasi: PageSetup::ORIENTATION_LANDSCAPE,
        );
    }
}
