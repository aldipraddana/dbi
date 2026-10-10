<?php

namespace App\Exports;

use App\Enums\JenisLaporanEnum;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanKeuanganExport
{
    protected Spreadsheet $spreadsheet;

    public function __construct(
        protected string $jenisLaporan,
        protected string $tanggalMulai,
        protected string $tanggalAkhir,
    ) {}

    public function download(): StreamedResponse
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();

        $this->writeHeader($sheet);
        $this->writeBody($sheet);

        $fileName = $this->generateFileName();

        // Pastikan ekstensi .xlsx dan nama file aman
        $fileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $fileName);
        if (! str_ends_with(strtolower($fileName), '.xlsx')) {
            $fileName .= '.xlsx';
        }

        return response()->streamDownload(
            function (): void {
                // Bersihkan output buffer supaya file tidak korup
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }

                $writer = new Xlsx($this->spreadsheet);
                $writer->setPreCalculateFormulas(false);
                $writer->save('php://output');

                $this->spreadsheet->disconnectWorksheets();
                unset($this->spreadsheet);
            },
            $fileName,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0, no-cache, must-revalidate',
                'Pragma' => 'public',
            ]
        );
    }

    protected function writeHeader(Worksheet $sheet): void
    {
        $companyName = config('app.name');
        $jenis = JenisLaporanEnum::tryFrom($this->jenisLaporan);
        $reportName = $jenis?->label() ?? 'Laporan Keuangan';
        $tanggalMulaiFormatted = Carbon::parse($this->tanggalMulai)->translatedFormat('d F Y');
        $tanggalAkhirFormatted = Carbon::parse($this->tanggalAkhir)->translatedFormat('d F Y');

        $sheet->setCellValue('A1', $companyName);
        $sheet->setCellValue('A2', $reportName);
        $sheet->setCellValue('A3', "Periode: {$tanggalMulaiFormatted} s/d {$tanggalAkhirFormatted}");

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['size' => 10],
        ]);

        $sheet->getColumnDimension('A')->setWidth(60);
    }

    protected function writeBody(Worksheet $sheet): void
    {
        $startRow = 5;

        $sheet->setCellValue('A' . $startRow, '[ISI LAPORAN AKAN DITAMBAHKAN KEMUDIAN]');
        $sheet->getStyle('A' . $startRow)->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '999999'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }

    protected function generateFileName(): string
    {
        $jenis = JenisLaporanEnum::tryFrom($this->jenisLaporan);
        $slug = $jenis?->fileName() ?? 'Laporan';

        return $slug . '_' . now()->format('d_m_Y_His') . '.xlsx';
    }
}
