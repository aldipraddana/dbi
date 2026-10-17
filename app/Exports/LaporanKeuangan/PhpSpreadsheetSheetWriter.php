<?php

declare(strict_types=1);

namespace App\Exports\LaporanKeuangan;

use App\Services\LaporanKeuangan\LaporanSheetWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Penulisan sheet laporan ke Excel (PhpSpreadsheet).
 *
 * Seluruh penulisan & styling dipindahkan utuh dari BaseLaporanSheet, sehingga
 * hasil Excel tidak berubah; BaseLaporanSheet kini hanya menyusun urutan baris.
 */
class PhpSpreadsheetSheetWriter implements LaporanSheetWriter
{
    protected const string WARNA_HEADER = '44546A';

    protected const string WARNA_SECTION = 'D9E2F3';

    public function __construct(private readonly Worksheet $sheet) {}

    public function writeReportHeader(string $namaPerusahaan, string $judul, string $periode): void
    {
        $this->sheet->setCellValue('A1', $namaPerusahaan);
        $this->sheet->setCellValue('A2', $judul);
        $this->sheet->setCellValue('A3', $periode);

        $this->sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
        ]);
        $this->sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
        $this->sheet->getStyle('A3')->applyFromArray([
            'font' => ['size' => 10, 'italic' => true, 'color' => ['rgb' => '595959']],
        ]);
    }

    public function writeTableHeader(array $headers, int $row): void
    {
        foreach ($headers as $kolom => $label) {
            $this->sheet->setCellValue($kolom.$row, $label);
        }

        $akhirKolom = (string) array_key_last($headers);

        $this->sheet
            ->getStyle(self::KOLOM_URAIAN.$row.':'.$akhirKolom.$row)
            ->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::WARNA_HEADER]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '808080'],
                    ],
                ],
            ]);
    }

    public function writeSection(int $row, string $teks, string $sampaiKolom): void
    {
        $this->sheet->setCellValue(self::KOLOM_URAIAN.$row, $teks);
        $this->sheet
            ->getStyle(self::KOLOM_URAIAN.$row.':'.$sampaiKolom.$row)
            ->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::WARNA_SECTION]],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'BFBFBF'],
                    ],
                ],
            ]);
    }

    public function writeLabel(int $row, string $teks, bool $tebal): void
    {
        $koordinat = self::KOLOM_URAIAN.$row;

        $this->sheet->setCellValue($koordinat, $teks);

        if ($tebal) {
            $this->sheet->getStyle($koordinat)->applyFromArray([
                'font' => ['bold' => true],
            ]);
        }
    }

    public function writeAmount(int $row, int|float $nilai, bool $tebal, string $kolom): void
    {
        $koordinat = $kolom.$row;

        $this->sheet->setCellValue($koordinat, (float) $nilai);
        $this->sheet->getStyle($koordinat)->applyFromArray([
            'numberFormat' => ['formatCode' => self::FORMAT_NILAI],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'font' => ['bold' => $tebal],
        ]);
    }

    public function writePercent(int $row, int|float $rasio, string $kolom): void
    {
        $koordinat = $kolom.$row;

        $this->sheet->setCellValue($koordinat, (float) $rasio);
        $this->sheet->getStyle($koordinat)->applyFromArray([
            'numberFormat' => ['formatCode' => self::FORMAT_PERSENTASE],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
    }

    public function styleTotal(int $row, string $sampaiKolom): void
    {
        $this->sheet
            ->getStyle(self::KOLOM_URAIAN.$row.':'.$sampaiKolom.$row)
            ->applyFromArray([
                'font' => ['bold' => true],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '404040'],
                    ],
                ],
            ]);
    }

    public function writeNote(int $row, string $teks, string $sampaiKolom): void
    {
        $this->sheet->setCellValue(self::KOLOM_URAIAN.$row, $teks);
        $this->sheet
            ->getStyle(self::KOLOM_URAIAN.$row.':'.$sampaiKolom.$row)
            ->applyFromArray([
                'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '7F7F7F']],
            ]);
    }

    public function finish(array $kolomNilai, string $orientasi, int $lebarKolomUraian): void
    {
        $this->sheet->getColumnDimension(self::KOLOM_URAIAN)->setWidth($lebarKolomUraian);
        $this->autosizeColumns($kolomNilai);

        $this->sheet->freezePane(self::KOLOM_URAIAN.BaseLaporanSheet::FIRST_CONTENT_ROW);

        $pageSetup = $this->sheet->getPageSetup();
        $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);
        $pageSetup->setOrientation($orientasi);
        $pageSetup->setFitToPage(true);
        $pageSetup->setFitToWidth(1);
        $pageSetup->setFitToHeight(0);

        $this->sheet->setSelectedCells(self::KOLOM_URAIAN.BaseLaporanSheet::FIRST_CONTENT_ROW);
    }

    /**
     * Autosize kolom angka berdasarkan nilai yang tampil.
     *
     * @param  array<int, string>  $kolom
     */
    private function autosizeColumns(array $kolom): void
    {
        $barisTerakhir = $this->sheet->getHighestDataRow();

        foreach ($kolom as $kolomSatu) {
            $lebarTerpanjang = 0;

            for ($baris = 1; $baris <= $barisTerakhir; $baris++) {
                $koordinat = $kolomSatu.$baris;

                if (! $this->sheet->cellExists($koordinat)) {
                    continue;
                }

                $nilai = $this->sheet->getCell($koordinat)->getValue();

                if (is_numeric($nilai)) {
                    $lebar = strlen(number_format((float) $nilai, 0, ',', ''));
                } elseif (is_string($nilai)) {
                    $lebar = strlen($nilai);
                } else {
                    continue;
                }

                $lebarTerpanjang = max($lebarTerpanjang, $lebar);
            }

            $this->sheet->getColumnDimension($kolomSatu)->setWidth(max(14, $lebarTerpanjang + 3));
        }
    }
}
