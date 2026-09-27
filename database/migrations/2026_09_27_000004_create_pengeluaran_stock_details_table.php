<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran_stock_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengeluaran_stock_id')->constrained('pengeluaran_stocks')->onDelete('cascade');
            $table->foreignId('pengadaan_stock_detail_id')->constrained('pengadaan_stock_details')->onDelete('restrict');
            $table->integer('qty');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_stock_details');
    }
};
