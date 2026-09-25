<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            // Drop the old string column
            $table->dropColumn('kategori_pengeluaran');

            // Add the foreign key column
            $table->foreignId('kategori_pengeluaran_id')
                ->nullable()
                ->after('tanggal')
                ->constrained('kategori_pengeluarans')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropForeign(['kategori_pengeluaran_id']);
            $table->dropColumn('kategori_pengeluaran_id');

            // Restore the old string column
            $table->string('kategori_pengeluaran')->nullable();
        });
    }
};
