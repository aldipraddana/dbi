<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dana_keluars', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->required();
            $table->enum('jenis', ['operasional', 'bahan_baku', 'gaji', 'biaya_kantor', 'biaya_lain'])->required();
            $table->enum('kategori_gaji', ['uang_makan', 'lembur', 'pelunasan_pekerjaan', 'kas_bon', 'gaji_bulanan'])->nullable();
            $table->enum('tipe_pekerja', ['harian', 'borongan'])->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('kendaraan_id')->nullable()->constrained('kendaraans')->nullOnDelete();
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('nama_proyek')->nullable();
            $table->string('nama_mandor')->nullable();
            $table->string('nama_pemborong')->nullable();
            $table->string('nama_pekerjaan')->nullable();
            $table->string('keperluan')->nullable();
            $table->string('item')->nullable();
            $table->decimal('qty', 20, 4)->nullable();
            $table->decimal('nominal', 15, 2)->required()->default(0);
            $table->decimal('potongan_kas_bon', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->required()->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tanggal');
            $table->index('jenis');
            $table->index('kategori_gaji');
        });

        Schema::create('dana_keluar_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dana_keluar_id')->required()->constrained('dana_keluars')->cascadeOnDelete();
            $table->foreignId('pengadaan_stock_id')->nullable()->constrained('pengadaan_stocks')->nullOnDelete();
            $table->string('nama_barang')->required();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->decimal('qty', 20, 4)->required()->default(1);
            $table->decimal('harga_satuan', 15, 2)->required()->default(0);
            $table->decimal('subtotal', 15, 2)->required()->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dana_keluar_items');
        Schema::dropIfExists('dana_keluars');
    }
};
