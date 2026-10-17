<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenisLaporanEnum;
use App\Exports\LaporanKeuangan\LaporanKeuanganExport;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\Concerns\LaporanKeuanganTestData;
use Tests\TestCase;

/**
 * Migrasi bawaan aplikasi belum bisa dijalankan penuh di sqlite, sehingga test
 * ini membuat tabel yang dipakai laporan secara langsung di sqlite :memory:.
 */
class LaporanKeuanganExcelTest extends TestCase
{
    use LaporanKeuanganTestData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createLaporanSchema();
    }

    public function test_tujuan_download_tanpa_login_redirect_ke_login(): void
    {
        $this->get(route('laporan.keuangan.download-excel', $this->queryDasar()))
            ->assertRedirect();

        $this->assertGuest();
    }

    public function test_tujuan_download_tanpa_login_tidak_menghasilkan_file(): void
    {
        $response = $this->get(route('laporan.keuangan.download-excel', $this->queryDasar()));

        $response->assertRedirect();
        $this->assertStringNotContainsString(
            'spreadsheet',
            (string) $response->headers->get('Content-Type', ''),
        );
    }

    public function test_validasi_tanggal_akhir_tidak_boleh_sebelum_tanggal_mulai(): void
    {
        $this->actingAsUser()
            ->get(route('laporan.keuangan.download-excel', [
                'jenis' => 'neraca',
                'tanggal_mulai' => '2026-10-16',
                'tanggal_akhir' => '2026-10-01',
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('tanggal_akhir');
    }

    public function test_validasi_jenis_laporan_tidak_dikenal(): void
    {
        $this->actingAsUser()
            ->get(route('laporan.keuangan.download-excel', [
                'jenis' => 'bukan_jenis_laporan',
                'tanggal_mulai' => self::TANGGAL_MULAI,
                'tanggal_akhir' => self::TANGGAL_AKHIR,
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('jenis');
    }

    public function test_download_excel_mengembalikan_file_xlsx(): void
    {
        $this->actingAsUser()
            ->get(route('laporan.keuangan.download-excel', $this->queryDasar()))
            ->assertOk()
            ->assertDownload('Laporan-Keuangan-semua-20261001-20261016.xlsx');
    }

    public function test_semua_jenis_laporan_menghasilkan_lima_sheet(): void
    {
        $spreadsheet = $this->simpanDanMuat($this->buildWorkbook(JenisLaporanEnum::Semua));

        $namaSheet = array_values($spreadsheet->getSheetNames());

        $this->assertSame(
            array_map(
                fn (JenisLaporanEnum $jenis): string => $jenis->sheetName(),
                JenisLaporanEnum::laporan(),
            ),
            $namaSheet,
        );
        $this->assertSame('Neraca', $spreadsheet->getActiveSheet()->getTitle());
    }

    public function test_jenis_laporan_tunggal_menghasilkan_satu_sheet(): void
    {
        foreach (JenisLaporanEnum::laporan() as $jenis) {
            $spreadsheet = $this->simpanDanMuat($this->buildWorkbook($jenis));

            $this->assertSame([$jenis->sheetName()], array_values($spreadsheet->getSheetNames()));
        }
    }

    public function test_header_sheet_memakai_label_judul_dan_periode_bahasa_indonesia(): void
    {
        $spreadsheet = $this->simpanDanMuat($this->buildWorkbook(JenisLaporanEnum::Neraca));
        $sheet = $spreadsheet->getSheetByName('Neraca');

        $this->assertNotNull($sheet);
        $this->assertSame((string) config('app.name'), $sheet->getCell('A1')->getValue());
        $this->assertSame(
            JenisLaporanEnum::Neraca->label(),
            $sheet->getCell('A2')->getValue(),
        );
        $this->assertSame(
            'Periode: 01 Oktober 2026 s/d 16 Oktober 2026',
            $sheet->getCell('A3')->getValue(),
        );
    }

    public function test_konsistensi_angka_antar_sheet(): void
    {
        $this->seedLaporanKecil();

        $spreadsheet = $this->simpanDanMuat($this->buildWorkbook(JenisLaporanEnum::Semua));

        $kasNeraca = $this->cariNilai($spreadsheet, 'Neraca', 'Kas');
        $saldoAkhir = $this->cariNilai($spreadsheet, 'Cash Flow', 'Saldo Kas Akhir');

        $this->assertSame($kasNeraca, $saldoAkhir);

        $labaBersih = $this->cariNilai($spreadsheet, 'Profit and Loss', 'Laba Bersih');
        $labaPeriode = $this->cariNilai($spreadsheet, 'Perubahan Ekuitas', 'Laba / (Rugi) Periode');

        $this->assertSame($labaBersih, $labaPeriode);
    }

    public function test_filter_status_pembayaran_dan_soft_delete_pada_pengeluaran(): void
    {
        // kas sebelum periode laporan, di luar rentang tanggal
        $this->insertDanaMasuk([
            'tanggal_masuk' => '2026-09-01',
            'customer' => 'Pelanggan Luar Periode',
            'jumlah_dana_masuk' => 999_999,
            'status_pembayaran' => 'lunas',
        ]);

        // dalam periode: lunas dan sama-sama sudah dibayar (uang muka / dp)
        $this->insertDanaMasuk([
            'customer' => 'Pelanggan Lunas',
            'jumlah_dana_masuk' => 100_000,
            'status_pembayaran' => 'lunas',
        ]);
        $this->insertDanaMasuk([
            'customer' => 'Pelanggan DP',
            'jumlah_dana_masuk' => 50_000,
            'status_pembayaran' => 'dp',
        ]);

        $this->insertDanaKeluar(['total' => 20_000, 'jenis' => 'operasional']);
        // pengeluaran soft-deleted tidak boleh ikut dihitung
        $this->insertDanaKeluar(['total' => 30_000, 'jenis' => 'operasional', 'deleted_at' => now()]);

        $spreadsheet = $this->simpanDanMuat($this->buildWorkbook(JenisLaporanEnum::Semua));

        // 100.000 (lunas) + 50.000 (dp) = 150.000; 999.999 di luar periode tidak ikut
        $this->assertSame(
            150_000.0,
            $this->cariNilai($spreadsheet, 'Profit and Loss', 'Total Pendapatan'),
        );

        // hanya 20_000 yang hidup; 30_000 soft-deleted dikeluarkan
        $this->assertSame(
            20_000.0,
            $this->cariNilai($spreadsheet, 'Profit and Loss', 'Operasional'),
        );

        $this->assertSame(
            130_000.0,
            $this->cariNilai($spreadsheet, 'Profit and Loss', 'Laba Bersih'),
        );

        $this->assertSame(
            999_999.0,
            $this->cariNilai($spreadsheet, 'Cash Flow', 'Saldo Kas Awal'),
        );
        $this->assertSame(
            1_129_999.0,
            $this->cariNilai($spreadsheet, 'Cash Flow', 'Saldo Kas Akhir'),
        );
        $this->assertSame(
            1_129_999.0,
            $this->cariNilai($spreadsheet, 'Neraca', 'Kas'),
        );
    }

    private function buildWorkbook(JenisLaporanEnum $jenis): Spreadsheet
    {
        return (new LaporanKeuanganExport(
            jenis: $jenis,
            mulai: Carbon::parse(self::TANGGAL_MULAI),
            akhir: Carbon::parse(self::TANGGAL_AKHIR),
        ))->build();
    }

    /**
     * Simpan lalu muat ulang file .xlsx, supaya pengujian memakai hasil tulis
     * yang sama dengan file yang benar-benar di-download.
     */
    private function simpanDanMuat(Spreadsheet $spreadsheet): Spreadsheet
    {
        $path = tempnam(sys_get_temp_dir(), 'laporan-keuangan-') . '.xlsx';

        try {
            (new Xlsx($spreadsheet))->save($path);

            return IOFactory::load($path);
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private function cariNilai(Spreadsheet $spreadsheet, string $namaSheet, string $label): float
    {
        $sheet = $spreadsheet->getSheetByName($namaSheet);

        $this->assertNotNull($sheet, "Sheet [{$namaSheet}] tidak ditemukan.");

        $baris = $sheet->rangeToArray(
            'A1:B' . $sheet->getHighestDataRow(),
            nullValue: null,
            calculateFormulas: false,
            formatData: false,
        );

        foreach ($baris as [$uraian, $nilai]) {
            if ($uraian === $label) {
                $this->assertIsNumeric($nilai, "Nilai untuk [{$label}] bukan angka.");
                $this->assertIsFloat($nilai);

                return (float) $nilai;
            }
        }

        $this->fail("Label [{$label}] tidak ditemukan pada sheet [{$namaSheet}].");
    }
}
