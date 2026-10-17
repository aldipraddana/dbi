<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Schema minimum + fixture data untuk pengujian laporan keuangan.
 *
 * Migrasi bawaan aplikasi belum bisa dijalankan penuh di sqlite, sehingga
 * tabel yang dipakai laporan dibuat langsung di sqlite :memory:.
 */
trait LaporanKeuanganTestData
{
    protected const string TANGGAL_MULAI = '2026-10-01';

    protected const string TANGGAL_AKHIR = '2026-10-16';

    /**
     * Parameter request yang dipakai action Excel maupun Print / PDF.
     *
     * @return array<string, string>
     */
    protected function queryDasar(): array
    {
        return [
            'jenis' => 'semua',
            'tanggal_mulai' => self::TANGGAL_MULAI,
            'tanggal_akhir' => self::TANGGAL_AKHIR,
        ];
    }

    /**
     * Login sebagai user uji, lalu kembalikan test case supaya bisa di-chain
     * ke `$this->get(...)`.
     */
    protected function actingAsUser(): static
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
    protected function insertDanaMasuk(array $atribut = []): int
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
    protected function insertDanaKeluar(array $atribut = []): int
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

    protected function seedLaporanKecil(): void
    {
        $this->insertDanaMasuk(['jumlah_dana_masuk' => 100_000, 'status_pembayaran' => 'lunas']);
        $this->insertDanaKeluar(['total' => 30_000, 'jenis' => 'operasional']);
    }

    /**
     * Schema minimum untuk laporan keuangan (tanpa foreign key), karena
     * migrasi bawaan aplikasi belum tentu jalan di sqlite.
     */
    protected function createLaporanSchema(): void
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
