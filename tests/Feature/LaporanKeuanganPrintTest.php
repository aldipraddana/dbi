<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenisLaporanEnum;
use App\Exports\LaporanKeuangan\LaporanKeuanganExport;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use DOMXPath;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\Concerns\LaporanKeuanganTestData;
use Tests\TestCase;

/**
 * Verifikasi halaman print identik dengan export Excel untuk $params yang sama:
 * judul/periode, urutan & label baris, nilai rupiah, persentase, jumlah kolom,
 * dan jumlah baris. Keduanya dibaca dari sumber yang sama (sel Excel vs HTML print),
 * sehingga perbedaan apapun langsung ketahuan.
 */
class LaporanKeuanganPrintTest extends TestCase
{
    use LaporanKeuanganTestData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createLaporanSchema();
    }

    public function test_tujuan_print_tanpa_login_redirect_ke_login(): void
    {
        $this->get(route('laporan.keuangan.print', $this->queryDasar()))
            ->assertRedirect();

        $this->assertGuest();
    }

    public function test_validasi_print_memakai_validasi_yang_sama_dengan_excel(): void
    {
        $this->actingAsUser();

        // tanggal akhir sebelum tanggal mulai -> kedua route menolak dengan error yang sama
        $this->get(route('laporan.keuangan.print', [
            'jenis' => 'neraca',
            'tanggal_mulai' => '2026-10-16',
            'tanggal_akhir' => '2026-10-01',
        ]))
            ->assertRedirect()
            ->assertSessionHasErrors('tanggal_akhir');

        // jenis laporan tidak dikenal -> kedua route menolak di field "jenis"
        $this->get(route('laporan.keuangan.print', [
            'jenis' => 'bukan_jenis_laporan',
            'tanggal_mulai' => self::TANGGAL_MULAI,
            'tanggal_akhir' => self::TANGGAL_AKHIR,
        ]))
            ->assertRedirect()
            ->assertSessionHasErrors('jenis');

        // parameter wajib yang tidak diisi
        $this->get(route('laporan.keuangan.print', [
            'jenis' => 'neraca',
            'tanggal_mulai' => self::TANGGAL_MULAI,
        ]))
            ->assertRedirect()
            ->assertSessionHasErrors(['tanggal_akhir']);
    }

    public function test_print_dan_excel_menerima_parameter_yang_sama(): void
    {
        $this->actingAsUser();

        foreach (JenisLaporanEnum::cases() as $jenis) {
            $params = [
                'jenis' => $jenis->value,
                'tanggal_mulai' => self::TANGGAL_MULAI,
                'tanggal_akhir' => self::TANGGAL_AKHIR,
            ];

            $this->get(route('laporan.keuangan.print', $params))->assertOk();
            $this->get(route('laporan.keuangan.download-excel', $params))->assertOk();
        }
    }

    public function test_header_print_sama_dengan_header_sheet_excel(): void
    {
        $sheet = $this->buildWorkbook(JenisLaporanEnum::Neraca)->getSheetByName('Neraca');
        $this->assertNotNull($sheet);

        $this->actingAsUser();

        $laporan = $this->bacaPrint([
            'jenis' => 'neraca',
            'tanggal_mulai' => self::TANGGAL_MULAI,
            'tanggal_akhir' => self::TANGGAL_AKHIR,
        ]);

        $this->assertCount(1, $laporan);
        $this->assertSame([
            (string) $sheet->getCell('A1')->getValue(),
            (string) $sheet->getCell('A2')->getValue(),
            (string) $sheet->getCell('A3')->getValue(),
        ], $laporan[0]['header']);

        // judul & periode memakai format tanggal Indonesia, sama seperti Excel
        $this->assertSame(
            'Periode: 01 Oktober 2026 s/d 16 Oktober 2026',
            $laporan[0]['header'][2],
        );
    }

    public function test_isi_print_identik_dengan_excel(): void
    {
        $this->actingAsUser();
        $this->seedLaporanUji();

        foreach ([...JenisLaporanEnum::laporan(), JenisLaporanEnum::Semua] as $jenis) {
            $this->assertPrintSamaDenganExcel($jenis);
        }
    }

    public function test_print_jenis_semua_menghasilkan_lima_laporan(): void
    {
        $this->actingAsUser();

        $laporan = $this->bacaPrint($this->queryDasar());

        $this->assertSame(
            array_map(fn (JenisLaporanEnum $jenis): string => $jenis->value, JenisLaporanEnum::laporan()),
            array_column($laporan, 'jenis'),
        );
    }

    public function test_format_angka_print_mengikuti_format_sel_excel(): void
    {
        $this->actingAsUser();
        $this->seedLaporanUji();

        $neraca = $this->nilaiPerLabel($this->bacaPrint([
            'jenis' => 'neraca',
            'tanggal_mulai' => self::TANGGAL_MULAI,
            'tanggal_akhir' => self::TANGGAL_AKHIR,
        ]));

        // format Excel `#,##0;(#,##0);-`: nol jadi "-"
        $this->assertSame('-', $neraca['Piutang Usaha']);
        $this->assertSame('-', $neraca['Total Liabilitas']);
        // negatif jadi "(20.000)" (kas 150.000 - keluar 170.000)
        $this->assertSame('(20.000)', $neraca['Kas']);

        $labaRugiPrint = $this->bacaPrint([
            'jenis' => 'profit_loss',
            'tanggal_mulai' => self::TANGGAL_MULAI,
            'tanggal_akhir' => self::TANGGAL_AKHIR,
        ]);

        // positif: pemisah ribuan titik, tanpa desimal
        $this->assertSame('150.000', $this->nilaiPerLabel($labaRugiPrint)['Total Pendapatan']);
        // persentase memakai format Excel `0.0%`
        $this->assertSame('100,0%', $this->persenPerLabel($labaRugiPrint)['Total Pendapatan']);
        // laba bersih = 150.000 - 170.000 -> negatif memakai kurung
        $this->assertSame('(20.000)', $this->nilaiPerLabel($labaRugiPrint)['Laba Bersih']);
    }

    public function test_html_print_memakai_style_cetak(): void
    {
        $this->actingAsUser();

        $html = $this->muatPrint($this->queryDasar());

        $this->assertStringContainsString('@page', $html);
        $this->assertStringContainsString('size: A4', $html);
        // judul kolom diulang pada tiap halaman cetak
        $this->assertStringContainsString('table-header-group', $html);
        // baris tidak terpotong di antar halaman
        $this->assertStringContainsString('page-break-inside: avoid', $html);
        // tombol print tidak ikut tercetak
        $this->assertStringContainsString('class="toolbar no-print"', $html);
        // tidak ada layout/navigasi Filament pada halaman print
        $this->assertStringNotContainsString('filament', strtolower($html));
    }

    /**
     * Bandingkan isi halaman print dengan isi workbook Excel untuk $params yang sama.
     */
    private function assertPrintSamaDenganExcel(JenisLaporanEnum $jenis): void
    {
        $cetak = $this->bacaPrint([
            'jenis' => $jenis->value,
            'tanggal_mulai' => self::TANGGAL_MULAI,
            'tanggal_akhir' => self::TANGGAL_AKHIR,
        ]);

        $excel = $this->bacaWorkbook($this->buildWorkbook($jenis));

        $this->assertSame(
            array_column($excel, 'jenis'),
            array_column($cetak, 'jenis'),
            "Daftar laporan print berbeda dengan Excel untuk [{$jenis->value}].",
        );

        foreach ($excel as $indeks => $sheetExcel) {
            $laporanCetak = $cetak[$indeks];
            $judul = $sheetExcel['jenis'];

            $this->assertSame($sheetExcel['header'], $laporanCetak['header'], "Header [{$judul}] berbeda.");
            $this->assertSame($sheetExcel['kolom'], $laporanCetak['kolom'], "Judul kolom [{$judul}] berbeda.");
            $this->assertSame(
                $sheetExcel['baris'],
                $laporanCetak['baris'],
                "Isi laporan [{$judul}] berbeda (urutan/label/nilai).",
            );
            $this->assertCount(count($sheetExcel['baris']), $laporanCetak['baris'], "Jumlah baris [{$judul}] berbeda.");
        }
    }

    /**
     * Susun ulang isi workbook menjadi bandingan langsung dengan hasil print.
     *
     * @return array<int, array{jenis: string, header: array<int, string>, kolom: array<int, string>, baris: array<int, array{label: string, nilai: ?string, persen: ?string}>}>
     */
    private function bacaWorkbook(Spreadsheet $spreadsheet): array
    {
        $hasil = [];

        foreach ($spreadsheet->getSheetNames() as $namaSheet) {
            $sheet = $spreadsheet->getSheetByName($namaSheet);
            $this->assertNotNull($sheet, "Sheet [{$namaSheet}] tidak ditemukan.");

            $barisExcel = $sheet->rangeToArray(
                'A1:C'.$sheet->getHighestDataRow(),
                nullValue: null,
                calculateFormulas: false,
                formatData: false,
            );

            $headerKolom = [];

            foreach ($barisExcel as $baris) {
                if ($baris[0] === 'Uraian' || $baris[0] === 'Keterangan') {
                    $headerKolom = array_values(array_filter($baris, fn ($nilai): bool => $nilai !== null));
                    break;
                }
            }

            $this->assertNotSame([], $headerKolom, "Judul kolom sheet [{$namaSheet}] tidak ditemukan.");

            $isi = [];

            // baris 1-3 = header laporan, baris 4 = header tabel (ada di <thead> print)
            foreach (array_slice($barisExcel, 4) as $baris) {
                if ($baris[0] === null || $baris[0] === '') {
                    continue;
                }

                if ($baris[0] === 'Uraian' || $baris[0] === 'Keterangan') {
                    continue;
                }

                $isi[] = [
                    'label' => (string) $baris[0],
                    'nilai' => $baris[1] === null ? null : format_nilai_laporan((float) $baris[1]),
                    'persen' => $baris[2] === null ? null : format_persen_laporan((float) $baris[2]),
                ];
            }

            $hasil[] = [
                'jenis' => $this->jenisDariNamaSheet($namaSheet),
                'header' => [
                    (string) $sheet->getCell('A1')->getValue(),
                    (string) $sheet->getCell('A2')->getValue(),
                    (string) $sheet->getCell('A3')->getValue(),
                ],
                'kolom' => $headerKolom,
                'baris' => $isi,
            ];
        }

        return $hasil;
    }

    private function jenisDariNamaSheet(string $namaSheet): string
    {
        foreach (JenisLaporanEnum::laporan() as $jenis) {
            if ($jenis->sheetName() === $namaSheet) {
                return $jenis->value;
            }
        }

        $this->fail("Nama sheet [{$namaSheet}] tidak dikenal.");
    }

    /**
     * Muat halaman print lalu baca isinya.
     *
     * @param  array<string, string>  $params
     * @return array<int, array{jenis: string, header: array<int, string>, kolom: array<int, string>, baris: array<int, array{label: string, nilai: ?string, persen: ?string}>}>
     */
    private function bacaPrint(array $params): array
    {
        return $this->parsePrint($this->muatPrint($params));
    }

    /**
     * @param  array<string, string>  $params
     */
    private function muatPrint(array $params): string
    {
        $response = $this->get(route('laporan.keuangan.print', $params));
        $response->assertOk();

        return (string) $response->getContent();
    }

    /**
     * Baca halaman print: header laporan + seluruh baris tabel per laporan.
     *
     * @return array<int, array{jenis: string, header: array<int, string>, kolom: array<int, string>, baris: array<int, array{label: string, nilai: ?string, persen: ?string}>}>
     */
    private function parsePrint(string $html): array
    {
        $dom = new DOMDocument;

        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $hasil = [];

        foreach ($xpath->query('//section[@data-jenis]') as $section) {
            $this->assertInstanceOf(DOMElement::class, $section);

            $header = [];
            foreach ($xpath->query('.//header[@class="header-laporan"]/div', $section) as $kepala) {
                $header[] = $kepala->textContent;
            }

            $kolom = [];
            foreach ($xpath->query('.//thead/tr/th', $section) as $th) {
                $kolom[] = $th->textContent;
            }

            $baris = [];

            foreach ($xpath->query('.//tbody/tr', $section) as $tr) {
                $kelas = $tr->attributes->getNamedItem('class')?->nodeValue ?? '';

                if (str_contains($kelas, 'baris-kosong')) {
                    continue;
                }

                $sel = $xpath->query('./td', $tr);
                $label = (string) $sel->item(0)?->textContent;

                if (str_contains($kelas, 'baris-section') || str_contains($kelas, 'baris-note')) {
                    $baris[] = ['label' => $label, 'nilai' => null, 'persen' => null];

                    continue;
                }

                $baris[] = [
                    'label' => $label,
                    'nilai' => $this->teksSel($sel->item(1)),
                    'persen' => $this->teksSel($sel->item(2)),
                ];
            }

            $hasil[] = [
                'jenis' => (string) $section->attributes->getNamedItem('data-jenis')?->nodeValue,
                'header' => $header,
                'kolom' => $kolom,
                'baris' => $baris,
            ];
        }

        return $hasil;
    }

    private function teksSel(?\DOMNode $sel): ?string
    {
        $teks = trim((string) $sel?->textContent);

        return $teks === '' ? null : $teks;
    }

    /**
     * @param  array<int, array{baris: array<int, array{label: string, nilai: ?string}>}>  $laporan
     * @return array<string, ?string>
     */
    private function nilaiPerLabel(array $laporan): array
    {
        $hasil = [];

        foreach ($laporan[0]['baris'] as $baris) {
            $hasil[$baris['label']] = $baris['nilai'];
        }

        return $hasil;
    }

    /**
     * @param  array<int, array{baris: array<int, array{label: string, persen: ?string}>}>  $laporan
     * @return array<string, ?string>
     */
    private function persenPerLabel(array $laporan): array
    {
        $hasil = [];

        foreach ($laporan[0]['baris'] as $baris) {
            $hasil[$baris['label']] = $baris['persen'];
        }

        return $hasil;
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
     * Data uji berisi angka positif, nol, dan negatif.
     */
    private function seedLaporanUji(): void
    {
        $this->insertDanaMasuk(['jumlah_dana_masuk' => 100_000, 'status_pembayaran' => 'lunas']);
        $this->insertDanaMasuk([
            'jumlah_dana_masuk' => 50_000,
            'status_pembayaran' => 'dp',
            'tipe_pembayaran' => 'transfer',
            'customer' => 1,
        ]);
        $this->insertDanaKeluar(['total' => 150_000, 'jenis' => 'operasional']);
        $this->insertDanaKeluar([
            'total' => 20_000,
            'jenis' => 'gaji',
            'kategori_gaji' => 'gaji_bulanan',
            'tipe_pekerja' => 'harian',
        ]);
    }
}
