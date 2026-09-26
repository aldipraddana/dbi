<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->string('no_polisi', 20)->unique();
            $table->string('jenis_kendaraan', 100);
            $table->string('merk', 100)->nullable();
            $table->string('model_tipe', 100)->nullable();
            $table->year('tahun_pembuatan')->nullable();
            $table->string('warna', 50)->nullable();
            $table->string('no_rangka', 50)->nullable();
            $table->string('no_mesin', 50)->nullable();
            $table->string('kapasitas', 50)->nullable();
            $table->enum('status_kepemilikan', ['Milik Sendiri', 'Sewa'])->nullable();
            $table->date('tanggal_berlaku_stnk')->nullable();
            $table->date('tanggal_berlaku_pajak')->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->string('foto_kendaraan')->nullable();
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
