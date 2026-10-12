<?php

declare(strict_types=1);

namespace App\Services\LaporanKeuangan;

use App\Enums\DanaKeluarJenis;
use App\Enums\KategoriGaji;
use App\Enums\TipePekerja;
use App\Models\Client;
use App\Models\DanaKeluar;
use App\Models\DanaKeluarItem;
use App\Models\DanaMasuk;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Sumber angka untuk seluruh laporan keuangan (basis kas).
 *
 * Semua query dilakukan di sini (agregasi di database: SUM / GROUP BY) dan
 * hasilnya di-memoize, supaya angka yang tampil di tiap sheet konsisten.
 * Data `dana_keluars` yang soft-deleted ikut terfilter otomatis oleh global
 * scope SoftDeletes.
 */
class LaporanKeuanganDataService
{
    /**
     * Status `status_pembayaran` pada `dana_masuks` yang dihitung sebagai pendapatan.
     *
     * Hasil pengecekan distinct nilai di data dan kode (lihat `DanaMasukResource`):
     * `dp` dan `lunas`. Keduanya menandakan dana yang benar-benar sudah diterima
     * (`dp` = uang muka yang sudah dibayar di muka, `lunas` = pelunasan), sehingga
     * keduanya dihitung pada laporan berbasis kas. Status seperti `belum lunas`
     * atau `dibatalkan` tidak ada pada tabel `dana_masuks`; bila ditambahkan nanti,
     * harus dikecualikan di konstanta ini.
     *
     * Filter ini dipakai untuk pendapatan MAUPUN saldo kas, sehingga
     * "Saldo Kas Akhir (Cash Flow)" = "Kas (Neraca)" dan sejalan dengan spesifikasi
     * Neraca yang memakai kumulatif seluruh `dana_masuks`.
     *
     * Ubah konstanta ini bila aturannya berbeda.
     */
    public const array STATUS_PENDAPATAN_DIHITUNG = ['lunas', 'dp'];

    /**
     * Pemetaan `jenis` dana keluar ke pos laporan.
     *
     * - bahan_baku          -> HPP
     * - operasional, gaji, biaya_kantor -> Beban Operasional
     * - biaya_lain          -> Pendapatan/Beban Lain
     */
    public const array POSISI_PER_JENIS = [
        'bahan_baku' => 'hpp',
        'operasional' => 'beban_operasional',
        'gaji' => 'beban_operasional',
        'biaya_kantor' => 'beban_operasional',
        'biaya_lain' => 'pendapatan_beban_lain',
    ];

    /**
     * Rincian pendapatan selalu ditampilkan meski tidak ada data.
     *
     * @var array<string, string>
     */
    public const array TIPE_PEMBAYARAN_DIKENAL = [
        'tunai' => 'Tunai',
        'transfer' => 'Transfer',
    ];

    /**
     * @var array<string, mixed>
     */
    private array $cache = [];

    private string $mulaiDate;

    private string $akhirDate;

    private Carbon $mulai;

    private Carbon $akhir;

    public function __construct(Carbon $mulai, Carbon $akhir)
    {
        $this->mulai = $mulai->copy()->startOfDay();
        $this->akhir = $akhir->copy()->startOfDay();
        $this->mulaiDate = $this->mulai->toDateString();
        $this->akhirDate = $this->akhir->toDateString();
    }

    /*
    |--------------------------------------------------------------------------
    | Pendapatan (dana masuk)
    |--------------------------------------------------------------------------
    */

    public function pendapatan(): float
    {
        return (float) $this->queryPendapatan()->sum('jumlah_dana_masuk');
    }

    /**
     * @return array<string, float>
     */
    public function pendapatanPerTipePembayaran(): array
    {
        $dariData = $this->queryPendapatan()
            ->selectRaw('tipe_pembayaran as kunci, SUM(jumlah_dana_masuk) as jumlah')
            ->groupBy('tipe_pembayaran')
            ->orderBy('tipe_pembayaran')
            ->get()
            ->mapWithKeys(fn (DanaMasuk $row): array => [(string) $row->kunci => (float) $row->jumlah]);

        $hasil = [];

        foreach (self::TIPE_PEMBAYARAN_DIKENAL as $kunci => $label) {
            $hasil[$label] = (float) ($dariData[$kunci] ?? 0.0);
        }

        foreach ($dariData as $kunci => $nilai) {
            if (array_key_exists($kunci, self::TIPE_PEMBAYARAN_DIKENAL)) {
                continue;
            }

            $hasil[ucfirst($kunci)] = $nilai;
        }

        return $hasil;
    }

    /**
     * @return array<string, float>
     */
    public function pendapatanPerCustomer(): array
    {
        $dariData = $this->queryPendapatan()
            ->selectRaw('customer as kunci, SUM(jumlah_dana_masuk) as jumlah')
            ->groupBy('customer')
            ->orderBy('customer')
            ->get();

        $hasil = [];

        foreach ($dariData as $baris) {
            $kunci = $this->labelCustomer((string) $baris->kunci);
            $hasil[$kunci] = ($hasil[$kunci] ?? 0.0) + (float) $baris->jumlah;
        }

        return $hasil;
    }

    /**
     * Nilai kolom `customer` pada `dana_masuks` campuran: boleh nama pelanggan
     * bebas, atau id Client (form Dana Masuk menyimpan pilihan Select berupa id).
     * Bila berupa id Client yang valid, tampilkan nama client-nya.
     */
    private function labelCustomer(string $customer): string
    {
        if ($customer === '') {
            return 'Tanpa Customer';
        }

        if (ctype_digit($customer)) {
            $nama = Client::query()->whereKey((int) $customer)->value('nama_client');

            if (is_string($nama) && $nama !== '') {
                return $nama;
            }
        }

        return $customer;
    }

    /*
    |--------------------------------------------------------------------------
    | Pengeluaran (dana keluar)
    |--------------------------------------------------------------------------
    */

    /**
     * Total kas keluar pada periode. Nilai kas keluar memakai kolom `total`
     * (sudah mencakup qty * nominal maupun hasil pengurangan potongan kas bon),
     * bukan `nominal`.
     */
    public function pengeluaran(): float
    {
        return (float) $this->queryPengeluaran()->sum('total');
    }

    /**
     * @return array<string, float>
     */
    public function pengeluaranPerJenis(): array
    {
        $hasil = array_fill_keys(DanaKeluarJenis::values(), 0.0);

        $dariData = $this->queryPengeluaran()
            ->selectRaw('jenis as kunci, SUM(total) as jumlah')
            ->groupBy('jenis')
            ->get()
            ->mapWithKeys(fn (DanaKeluar $row): array => [(string) $row->kunci => (float) $row->jumlah]);

        foreach ($dariData as $kunci => $nilai) {
            $hasil[$kunci] = $nilai;
        }

        return $hasil;
    }

    public function hpp(): float
    {
        return $this->pengeluaranPosisi('hpp');
    }

    public function bebanOperasional(): float
    {
        return $this->pengeluaranPosisi('beban_operasional');
    }

    public function bebanLain(): float
    {
        return $this->pengeluaranPosisi('pendapatan_beban_lain');
    }

    /**
     * @return array<string, float>
     */
    public function bebanOperasionalPerJenis(): array
    {
        $semua = $this->pengeluaranPerJenis();
        $hasil = [];

        foreach (self::POSISI_PER_JENIS as $jenis => $posisi) {
            if ($posisi === 'beban_operasional') {
                $hasil[$jenis] = $semua[$jenis] ?? 0.0;
            }
        }

        return $hasil;
    }

    /**
     * @return array<string, float>
     */
    public function gajiPerKategori(): array
    {
        $hasil = array_fill_keys(KategoriGaji::values(), 0.0);

        $dariData = $this->queryPengeluaran()
            ->where('jenis', DanaKeluarJenis::GAJI->value)
            ->selectRaw('kategori_gaji as kunci, SUM(total) as jumlah')
            ->groupBy('kategori_gaji')
            ->get()
            ->mapWithKeys(fn (DanaKeluar $row): array => [
                ((string) $row->kunci) !== '' ? (string) $row->kunci : 'tidak_ditentukan' => (float) $row->jumlah,
            ]);

        foreach ($dariData as $kunci => $nilai) {
            $hasil[$kunci] = $nilai;
        }

        return $hasil;
    }

    /**
     * @return array<string, float>
     */
    public function gajiPerTipePekerja(): array
    {
        $hasil = array_fill_keys(TipePekerja::values(), 0.0);

        $dariData = $this->queryPengeluaran()
            ->where('jenis', DanaKeluarJenis::GAJI->value)
            ->selectRaw('tipe_pekerja as kunci, SUM(total) as jumlah')
            ->groupBy('tipe_pekerja')
            ->get()
            ->mapWithKeys(fn (DanaKeluar $row): array => [
                ((string) $row->kunci) !== '' ? (string) $row->kunci : 'tidak_ditentukan' => (float) $row->jumlah,
            ]);

        foreach ($dariData as $kunci => $nilai) {
            $hasil[$kunci] = $nilai;
        }

        return $hasil;
    }

    /**
     * Pembelian bahan baku per supplier, diambil dari `dana_keluar_items.subtotal`.
     *
     * @return array<string, float>
     */
    public function bahanBakuPerSupplier(): array
    {
        $baris = DanaKeluarItem::query()
            ->join('dana_keluars', 'dana_keluars.id', '=', 'dana_keluar_items.dana_keluar_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'dana_keluar_items.supplier_id')
            ->whereNull('dana_keluars.deleted_at')
            ->whereBetween('dana_keluars.tanggal', [$this->mulaiDate, $this->akhirDate])
            ->where('dana_keluars.jenis', DanaKeluarJenis::BAHAN_BAKU->value)
            ->groupBy('dana_keluar_items.supplier_id', 'suppliers.nama_supplier')
            ->selectRaw('suppliers.nama_supplier, SUM(dana_keluar_items.subtotal) as jumlah')
            ->get();

        $hasil = [];

        foreach ($baris as $barisItem) {
            $nama = (string) ($barisItem->nama_supplier ?? 'Tanpa Supplier');
            $hasil[$nama] = ($hasil[$nama] ?? 0.0) + (float) $barisItem->jumlah;
        }

        return $hasil;
    }

    /*
    |--------------------------------------------------------------------------
    | Ikhtisar laba rugi
    |--------------------------------------------------------------------------
    */

    public function labaKotor(): float
    {
        return $this->pendapatan() - $this->hpp();
    }

    public function labaOperasional(): float
    {
        return $this->labaKotor() - $this->bebanOperasional();
    }

    public function labaBersih(): float
    {
        return $this->labaOperasional() - $this->bebanLain();
    }

    /*
    |--------------------------------------------------------------------------
    | Kas (kumulatif)
    |--------------------------------------------------------------------------
    */

    /**
     * Kas kumulatif sampai tanggal tertentu (tanpa batas awal).
     */
    public function kasAkhir(): float
    {
        return $this->kasSampai($this->akhirDate);
    }

    /**
     * Kas kumulatif sebelum periode laporan (saldo kas awal).
     */
    public function saldoKasSebelumPeriode(): float
    {
        return $this->kasSampai($this->mulai->copy()->subDay()->toDateString());
    }

    public function kenaikanKasPeriode(): float
    {
        return $this->pendapatan() - $this->pengeluaran();
    }

    /*
    |--------------------------------------------------------------------------
    | Arus kas (rincian)
    |--------------------------------------------------------------------------
    */

    public function pembayaranPemasok(): float
    {
        return $this->hpp();
    }

    public function pembayaranGaji(): float
    {
        return $this->gajiTotal();
    }

    public function pembayaranBeban(): float
    {
        $perJenis = $this->pengeluaranPerJenis();

        return ($perJenis['operasional'] ?? 0.0)
            + ($perJenis['biaya_kantor'] ?? 0.0)
            + ($perJenis['biaya_lain'] ?? 0.0);
    }

    private function gajiTotal(): float
    {
        return $this->pengeluaranPerJenis()['gaji'] ?? 0.0;
    }

    private function pengeluaranPosisi(string $posisi): float
    {
        $perJenis = $this->pengeluaranPerJenis();
        $total = 0.0;

        foreach (self::POSISI_PER_JENIS as $jenis => $posisiJenis) {
            if ($posisiJenis === $posisi) {
                $total += $perJenis[$jenis] ?? 0.0;
            }
        }

        return $total;
    }

    private function kasSampai(?string $tanggal): float
    {
        $key = 'kas_' . ($tanggal ?? 'semua');

        return $this->remember($key, function () use ($tanggal): float {
            $masuk = DanaMasuk::query()
                ->whereIn('status_pembayaran', self::STATUS_PENDAPATAN_DIHITUNG)
                ->when(
                    $tanggal !== null,
                    fn (Builder $query): Builder => $query->where('tanggal_masuk', '<=', $tanggal),
                )
                ->sum('jumlah_dana_masuk');

            $keluar = DanaKeluar::query()
                ->when(
                    $tanggal !== null,
                    fn (Builder $query): Builder => $query->where('tanggal', '<=', $tanggal),
                )
                ->sum('total');

            return (float) $masuk - (float) $keluar;
        });
    }

    /**
     * @return Builder<DanaMasuk>
     */
    private function queryPendapatan(): Builder
    {
        return DanaMasuk::query()
            ->whereBetween('tanggal_masuk', [$this->mulaiDate, $this->akhirDate])
            ->whereIn('status_pembayaran', self::STATUS_PENDAPATAN_DIHITUNG);
    }

    /**
     * @return Builder<DanaKeluar>
     */
    private function queryPengeluaran(): Builder
    {
        return DanaKeluar::query()
            ->whereBetween('tanggal', [$this->mulaiDate, $this->akhirDate]);
    }

    /**
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    private function remember(string $key, callable $callback): mixed
    {
        if (! array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $callback();
        }

        return $this->cache[$key];
    }
}
