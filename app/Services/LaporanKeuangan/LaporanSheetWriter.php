<?php

declare(strict_types=1);

namespace App\Services\LaporanKeuangan;

/**
 * Kontrak penulisan satu sheet laporan keuangan.
 *
 * Kelas `*Sheet` (Neraca, ProfitLoss, CashFlow, ...) hanya menyusun urutan baris
 * lewat method-method di bawah. Medium penulisannya ditentukan oleh implementasi:
 *
 * - `PhpSpreadsheetSheetWriter` -> worksheet Excel (sumber kebenaran),
 * - `HtmlSheetWriter`           -> baris HTML untuk halaman print.
 *
 * Karena kedua medium memanggil method yang sama dengan argumen yang sama,
 * isi print dan isi Excel berasal dari satu logika laporan, bukan dua salinan.
 */
interface LaporanSheetWriter
{
    /**
     * Format sel angka Excel `#,##0;(#,##0);-`:
     * positif `1.234`, nol `-`, negatif `(1.234)`.
     */
    public const string FORMAT_NILAI = '#,##0;(#,##0);-';

    /** Format sel persentase Excel `0.0%` (nilai disimpan sebagai rasio 0-1). */
    public const string FORMAT_PERSENTASE = '0.0%';

    /** Kolom pertama (uraian) yang dipakai semua sheet. */
    public const string KOLOM_URAIAN = 'A';

    /** Orientasi halaman A4 pada `finish()` (nilai sama dengan PageSetup::ORIENTATION_*). */
    public const string ORIENTASI_PORTRAIT = 'portrait';

    public const string ORIENTASI_LANDSCAPE = 'landscape';

    /** Baris 1-3: nama perusahaan, judul laporan, dan periode. */
    public function writeReportHeader(string $namaPerusahaan, string $judul, string $periode): void;

    /**
     * Baris judul kolom (tebal, background gelap).
     *
     * @param  array<string, string>  $headers  mis. ['A' => 'Uraian', 'B' => 'Nilai (Rp)']
     */
    public function writeTableHeader(array $headers, int $row): void;

    /** Baris section (mis. "ASET") dengan background muda. */
    public function writeSection(int $row, string $teks, string $sampaiKolom): void;

    /** Baris uraian pada kolom A. */
    public function writeLabel(int $row, string $teks, bool $tebal): void;

    /** Baris angka dengan FORMAT_NILAI. */
    public function writeAmount(int $row, int|float $nilai, bool $tebal, string $kolom): void;

    /** Baris persentase dengan FORMAT_PERSENTASE. */
    public function writePercent(int $row, int|float $rasio, string $kolom): void;

    /** Garis total/subtotal: tebal + border atas. */
    public function styleTotal(int $row, string $sampaiKolom): void;

    /** Catatan keterangan kecil (italic) di bawah tabel. */
    public function writeNote(int $row, string $teks, string $sampaiKolom): void;

    /**
     * Finishing sheet (lebar kolom, freeze pane, page setup).
     *
     * @param  array<int, string>  $kolomNilai
     */
    public function finish(array $kolomNilai, string $orientasi, int $lebarKolomUraian): void;
}
