<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dana_masuks', function (Blueprint $table) {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('dana_masuks');
    }
};
