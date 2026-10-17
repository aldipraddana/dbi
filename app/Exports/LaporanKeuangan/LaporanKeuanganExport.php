<?php

declare(strict_types=1);

namespace App\Exports\LaporanKeuangan;

use App\Enums\JenisLaporanEnum;
use App\Services\LaporanKeuangan\LaporanKeuanganDataService;
use App\Services\LaporanKeuangan\LaporanSheetFactory;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Orchestrator export laporan keuangan ke format .xlsx.
 *
 * Jenis tunggal menghasilkan 1 sheet; `Semua` menghasilkan 5 sheet
 * (satu per laporan, urut seperti JenisLaporanEnum::laporan()).
 */
class LaporanKeuanganExport
{
    private Spreadsheet $spreadsheet;

    public function __construct(
        private readonly JenisLaporanEnum $jenis,
        private readonly Carbon $mulai,
        private readonly Carbon $akhir,
    ) {}

    /**
     * Susun seluruh sheet laporan pada satu Spreadsheet.
     */
    public function build(): Spreadsheet
    {
        Carbon::setLocale('id');

        $this->spreadsheet = new Spreadsheet();

        // hapus sheet default; setiap laporan memakai Worksheet baru dari createSheet()
        $this->spreadsheet->removeSheetByIndex(0);

        $service = new LaporanKeuanganDataService($this->mulai, $this->akhir);

        foreach (LaporanSheetFactory::jenisDipilih($this->jenis) as $jenisLaporan) {
            $worksheet = $this->spreadsheet->createSheet();
            $worksheet->setTitle($jenisLaporan->sheetName());

            LaporanSheetFactory::buat(
                $jenisLaporan,
                new PhpSpreadsheetSheetWriter($worksheet),
                $this->mulai,
                $this->akhir,
                $service,
            )->render();
        }

        // sheet pertama (Neraca) menjadi sheet aktif saat file dibuka
        $this->spreadsheet->setActiveSheetIndex(0);

        return $this->spreadsheet;
    }

    public function fileName(): string
    {
        return sprintf(
            'Laporan-Keuangan-%s-%s-%s.xlsx',
            $this->jenis->value,
            $this->mulai->format('Ymd'),
            $this->akhir->format('Ymd'),
        );
    }

    public function download(): StreamedResponse
    {
        $spreadsheet = $this->build();
        $fileName = $this->fileName();

        return response()->streamDownload(
            function () use ($spreadsheet): void {
                // bersihkan output buffer supaya file hasil save tidak korup
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }

                $writer = new Xlsx($spreadsheet);
                $writer->setPreCalculateFormulas(false);
                $writer->save('php://output');

                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
            },
            $fileName,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0',
                'Pragma' => 'public',
            ],
        );
    }
}
