<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenisLaporanEnum;
use App\Exports\LaporanKeuangan\LaporanKeuanganExport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

/**
 * Migrasi bawaan aplikasi belum bisa dijalankan penuh di sqlite, sehingga test
 * ini membuat tabel yang dipakai laporan secara langsung di sqlite :memory:.
 */
class LaporanKeuanganExcelTest extends TestCase
{
    private const string TANGGAL_MULAI = '2026-10-01';

    private const string TANGGAL_AKHIR = '2026-10-16';

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
            ->assertRedirect();

        $this->assertSessionHasErrors('tanggal_akhir');
    }

    public function test_validasi_jenis_laporan_tidak_dikenal(): void
    {
        $this->actingAsUser()
            ->get(route('laporan.keuangan.download-excel', [
                'jenis' => 'bukan_jenis_laporan',
                'tanggal_mulai' => self::TANGGAL_MULAI,
                'tanggal_akhir' => self::TANGGAL_AKHIR,
            ]))
            ->assertRedirect();

        $this->assertSessionHasErrors('jenis');
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

    /**
     * @return array<string, string>
     */
    private function queryDasar(): array
    {
        return [
            'jenis' => JenisLaporanEnum::Semua->value,
            'tanggal_mulai' => self::TANGGAL_MULAI,
            'tanggal_akhir' => self::TANGGAL_AKHIR,
        ];
    }

    private function actingAsUser(): User
    {
        $user = User::query()->create([
            'name' => 'Admin Uji',
            'username' => 'admin_uji',
            'password' => 'kata-sandi-rahasia',
        ]);

        return $this->actingAs($user);
    }

    /**
     * @param  array<string, mixed>  $atribut
     */
    private function insertDanaMasuk(array $atribut = []): int
    {
        return (int) DB::table('dana_masuks')->insertGetId(array_merge([
            'nomor' => 'DM-UJI',
            'tanggal_masuk' => self::TANGGAL_MULAI,
            'no_invoice' => 'INV-UJI',
            'customer' => 'Pelanggan Uji',
            'item' => 'Item Uji',
            'jumlah_dana_masuk' => 0,
            'tipe_pembayaran' => 'tunai',
            'status_pembayaran' => 'lunas',
            'created_at' => now(),
            'updated_at' => now(),
        ], $atribut));
    }

    /**
     * @param  array<string, mixed>  $atribut
     */
    private function insertDanaKeluar(array $atribut = []): int
    {
        return (int) DB::table('dana_keluars')->insertGetId(array_merge([
            'tanggal' => self::TANGGAL_MULAI,
            'jenis' => 'operasional',
            'nominal' => 0,
            'total' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ], $atribut));
    }

    private function seedLaporanKecil(): void
    {
        $this->insertDanaMasuk(['jumlah_dana_masuk' => 100_000, 'status_pembayaran' => 'lunas']);
        $this->insertDanaKeluar(['total' => 30_000, 'jenis' => 'operasional']);
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

    /**
     * Schema minimum untuk laporan keuangan (tanpa foreign key), karena
     * migrasi bawaan aplikasi belum tentu jalan di sqlite.
     */
    private function createLaporanSchema(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable()->unique();
            $table->string('password');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('clients', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_client');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('suppliers', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_supplier');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dana_masuks', function (Blueprint $table): void {
            $table->id();
            $table->string('nomor');
            $table->date('tanggal_masuk');
            $table->string('no_invoice');
            $table->string('customer');
            $table->string('po')->nullable();
            $table->string('item');
            $table->decimal('jumlah_dana_masuk', 15, 2);
            $table->string('tipe_pembayaran');
            $table->string('status_pembayaran');
            $table->timestamps();
        });

        Schema::create('dana_keluars', function (Blueprint $table): void {
            $table->id();
            $table->date('tanggal');
            $table->string('jenis');
            $table->string('kategori_gaji')->nullable();
            $table->string('tipe_pekerja')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('kendaraan_id')->nullable();
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('nama_proyek')->nullable();
            $table->string('nama_mandor')->nullable();
            $table->string('nama_pemborong')->nullable();
            $table->string('nama_pekerjaan')->nullable();
            $table->string('keperluan')->nullable();
            $table->string('item')->nullable();
            $table->decimal('qty', 20, 4)->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->decimal('potongan_kas_bon', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dana_keluar_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('dana_keluar_id');
            $table->unsignedBigInteger('pengadaan_stock_id')->nullable();
            $table->string('nama_barang');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('qty', 20, 4)->default(1);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }
}
