<?php

declare(strict_types=1);

namespace App\Exports\LaporanKeuangan;

use App\Enums\JenisLaporanEnum;
use App\Services\LaporanKeuangan\LaporanKeuanganDataService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Kerangka satu sheet laporan: header (baris 1-3), styling, format angka,
 * autosize, freeze pane, dan page setup. Isi laporan diisi oleh subclass
 * lewat method `render()`.
 */
abstract class BaseLaporanSheet
{
    public const string FORMAT_NILAI = '#,##0;(#,##0);-';

    public const string FORMAT_PERSENTASE = '0.0%';

    protected const int TABLE_HEADER_ROW = 4;

    protected const int FIRST_CONTENT_ROW = 5;

    protected const string KOLOM_URAIAN = 'A';

    protected const string KOLOM_NILAI = 'B';

    protected const string KOLOM_PERSENTASE = 'C';

    protected const string WARNA_HEADER = '44546A';

    protected const string WARNA_SECTION = 'D9E2F3';

    public function __construct(
        protected readonly Worksheet $sheet,
        protected readonly Carbon $mulai,
        protected readonly Carbon $akhir,
        protected readonly LaporanKeuanganDataService $data,
    ) {}

    /**
     * Isi laporan pada sheet ini.
     */
    abstract public function render(): void;

    /**
     * Judul laporan diambil dari label enum.
     */
    abstract protected function jenisLaporan(): JenisLaporanEnum;

    /**
     * Baris 1-3: nama perusahaan, judul laporan, dan periode (locale Indonesia).
     */
    protected function writeReportHeader(): void
    {
        Carbon::setLocale('id');

        // TODO: ambil nama perusahaan dari tabel/pengaturan bila sudah tersedia,
        // sementara fallback ke config('app.name').
        $namaPerusahaan = (string) config('app.name');

        $this->sheet->setCellValue('A1', $namaPerusahaan);
        $this->sheet->setCellValue('A2', $this->jenisLaporan()->label());
        $this->sheet->setCellValue('A3', sprintf(
            'Periode: %s s/d %s',
            $this->mulai->translatedFormat('d F Y'),
            $this->akhir->translatedFormat('d F Y'),
        ));

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

    /**
     * Header tabel: tebal, background, border tipis.
     *
     * @param  array<string, string>  $headers  mis. ['A' => 'Uraian', 'B' => 'Nilai (Rp)']
     */
    protected function writeTableHeader(array $headers, int $row = self::TABLE_HEADER_ROW): void
    {
        foreach ($headers as $kolom => $label) {
            $this->sheet->setCellValue($kolom . $row, $label);
        }

        $akhirKolom = (string) array_key_last($headers);

        $this->sheet
            ->getStyle(self::KOLOM_URAIAN . $row . ':' . $akhirKolom . $row)
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

    protected function writeSection(int $row, string $teks, string $sampaiKolom = self::KOLOM_NILAI): void
    {
        $this->sheet->setCellValue(self::KOLOM_URAIAN . $row, $teks);
        $this->sheet
            ->getStyle(self::KOLOM_URAIAN . $row . ':' . $sampaiKolom . $row)
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

    protected function writeLabel(int $row, string $teks, bool $tebal = false): void
    {
        $koordinat = self::KOLOM_URAIAN . $row;

        $this->sheet->setCellValue($koordinat, $teks);

        if ($tebal) {
            $this->sheet->getStyle($koordinat)->applyFromArray([
                'font' => ['bold' => true],
            ]);
        }
    }

    protected function writeAmount(int $row, int|float $nilai, bool $tebal = false, string $kolom = self::KOLOM_NILAI): void
    {
        $koordinat = $kolom . $row;

        $this->sheet->setCellValue($koordinat, (float) $nilai);
        $this->sheet->getStyle($koordinat)->applyFromArray([
            'numberFormat' => ['formatCode' => self::FORMAT_NILAI],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'font' => ['bold' => $tebal],
        ]);
    }

    protected function writePercent(int $row, int|float $rasio, string $kolom = self::KOLOM_PERSENTASE): void
    {
        $koordinat = $kolom . $row;

        $this->sheet->setCellValue($koordinat, (float) $rasio);
        $this->sheet->getStyle($koordinat)->applyFromArray([
            'numberFormat' => ['formatCode' => self::FORMAT_PERSENTASE],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
    }

    /**
     * Rasio terhadap pendapatan, dengan guard pembagian nol.
     */
    protected function rasioKePendapatan(int|float $nilai): float
    {
        $pendapatan = $this->data->pendapatan();

        if ($pendapatan == 0.0) {
            return 0.0;
        }

        return (float) $nilai / $pendapatan;
    }

    /**
     * Baris subtotal/total: tebal dengan border atas.
     */
    protected function styleTotal(int $row, string $sampaiKolom = self::KOLOM_NILAI): void
    {
        $this->sheet
            ->getStyle(self::KOLOM_URAIAN . $row . ':' . $sampaiKolom . $row)
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

    /**
     * Catatan keterangan kecil (italic) di bawah tabel.
     */
    protected function writeNote(int $row, string $teks, string $sampaiKolom = self::KOLOM_NILAI): void
    {
        $this->sheet->setCellValue(self::KOLOM_URAIAN . $row, $teks);
        $this->sheet
            ->getStyle(self::KOLOM_URAIAN . $row . ':' . $sampaiKolom . $row)
            ->applyFromArray([
                'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '7F7F7F']],
            ]);
    }

    /**
     * Autosize kolom angka berdasarkan nilai yang tampil.
     *
     * @param  array<int, string>  $kolom
     */
    protected function autosizeColumns(array $kolom): void
    {
        $barisTerakhir = $this->sheet->getHighestDataRow();

        foreach ($kolom as $kolomSatu) {
            $lebarTerpanjang = 0;

            for ($baris = 1; $baris <= $barisTerakhir; $baris++) {
                $koordinat = $kolomSatu . $baris;

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

    /**
     * Finishing: lebar kolom, freeze pane di bawah header tabel, dan page setup.
     *
     * @param  array<int, string>  $kolomNilai
     */
    protected function finish(
        array $kolomNilai = [self::KOLOM_NILAI],
        string $orientasi = PageSetup::ORIENTATION_PORTRAIT,
        int $lebarKolomUraian = 56,
    ): void {
        $this->sheet->getColumnDimension(self::KOLOM_URAIAN)->setWidth($lebarKolomUraian);
        $this->autosizeColumns($kolomNilai);

        $this->sheet->freezePane(self::KOLOM_URAIAN . (self::FIRST_CONTENT_ROW));

        $pageSetup = $this->sheet->getPageSetup();
        $pageSetup->setPaperSize(PageSetup::PAPERSIZE_A4);
        $pageSetup->setOrientation($orientasi);
        $pageSetup->setFitToPage(true);
        $pageSetup->setFitToWidth(1);
        $pageSetup->setFitToHeight(0);

        $this->sheet->setSelectedCells(self::KOLOM_URAIAN . self::FIRST_CONTENT_ROW);
    }
}
