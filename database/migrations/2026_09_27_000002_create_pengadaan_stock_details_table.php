<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengadaan_stock_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengadaan_stock_id')->constrained('pengadaan_stocks')->onDelete('cascade');
            $table->string('nama_barang');
            $table->string('tipe');
            $table->string('pm');
            $table->integer('qty');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengadaan_stock_details');
    }
};
