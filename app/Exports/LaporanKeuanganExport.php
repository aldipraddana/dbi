<?php

namespace App\Exports;

use App\Enums\JenisLaporanEnum;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanKeuanganExport
{
    protected Spreadsheet $spreadsheet;

    public function __construct(
        protected string $jenisLaporan,
        protected string $tanggalMulai,
        protected string $tanggalAkhir,
    ) {}

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();

        $this->writeHeader($sheet);
        $this->writeBody($sheet);

        $fileName = $this->generateFileName();

        return response()->streamDownload(
            function () {
                $writer = new Xlsx($this->spreadsheet);
                $writer->save('php://output');
            },
            $fileName,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
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
            'font' => ['italic' => true, 'color' => ['rgb' => '999999']],
            'alignment' => new Alignment(Alignment::HORIZONTAL_CENTER),
        ]);
    }

    protected function generateFileName(): string
    {
        $jenis = JenisLaporanEnum::tryFrom($this->jenisLaporan);
        $slug = $jenis?->fileName() ?? 'Laporan';

        return $slug . '_' . now()->format('d_m_Y_His') . '.xlsx';
    }
}
