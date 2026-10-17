<?php

declare(strict_types=1);

namespace App\Exports\LaporanKeuangan;

use App\Enums\JenisLaporanEnum;
use App\Services\LaporanKeuangan\LaporanKeuanganDataService;
use App\Services\LaporanKeuangan\LaporanSheetWriter;
use Carbon\Carbon;

/**
 * Kerangka satu sheet laporan: header (baris 1-3), format angka, dan aturan
 * perhitungan. Isi laporan diisi oleh subclass lewat method `render()`.
 *
 * Seluruh tulisan diteruskan ke `LaporanSheetWriter`, sehingga urutan baris yang
 * sama bisa dicetak ke Excel (PhpSpreadsheetSheetWriter) maupun ke halaman print
 * (HtmlSheetWriter) tanpa menduplikasi logika laporan.
 */
abstract class BaseLaporanSheet
{
    public const string FORMAT_NILAI = LaporanSheetWriter::FORMAT_NILAI;

    public const string FORMAT_PERSENTASE = LaporanSheetWriter::FORMAT_PERSENTASE;

    protected const int TABLE_HEADER_ROW = 4;

    public const int FIRST_CONTENT_ROW = 5;

    protected const string KOLOM_URAIAN = LaporanSheetWriter::KOLOM_URAIAN;

    protected const string KOLOM_NILAI = 'B';

    protected const string KOLOM_PERSENTASE = 'C';

    public function __construct(
        protected readonly LaporanSheetWriter $writer,
        protected readonly Carbon $mulai,
        protected readonly Carbon $akhir,
        protected readonly LaporanKeuanganDataService $data,
    ) {}

    /**
     * Isi laporan pada sheet ini.
     */
    abstract public function render(): void;

    /**
     * Judul laporan diambil dari label enum.
     */
    abstract protected function jenisLaporan(): JenisLaporanEnum;

    /**
     * Baris 1-3: nama perusahaan, judul laporan, dan periode (locale Indonesia).
     */
    protected function writeReportHeader(): void
    {
        Carbon::setLocale('id');

        // TODO: ambil nama perusahaan dari tabel/pengaturan bila sudah tersedia,
        // sementara fallback ke config('app.name').
        $namaPerusahaan = (string) config('app.name');

        $this->writer->writeReportHeader(
            namaPerusahaan: $namaPerusahaan,
            judul: $this->jenisLaporan()->label(),
            periode: sprintf(
                'Periode: %s s/d %s',
                $this->mulai->translatedFormat('d F Y'),
                $this->akhir->translatedFormat('d F Y'),
            ),
        );
    }

    /**
     * Header tabel: tebal, background, border tipis.
     *
     * @param  array<string, string>  $headers  mis. ['A' => 'Uraian', 'B' => 'Nilai (Rp)']
     */
    protected function writeTableHeader(array $headers, int $row = self::TABLE_HEADER_ROW): void
    {
        $this->writer->writeTableHeader($headers, $row);
    }

    protected function writeSection(int $row, string $teks, string $sampaiKolom = self::KOLOM_NILAI): void
    {
        $this->writer->writeSection($row, $teks, $sampaiKolom);
    }

    protected function writeLabel(int $row, string $teks, bool $tebal = false): void
    {
        $this->writer->writeLabel($row, $teks, $tebal);
    }

    protected function writeAmount(int $row, int|float $nilai, bool $tebal = false, string $kolom = self::KOLOM_NILAI): void
    {
        $this->writer->writeAmount($row, $nilai, $tebal, $kolom);
    }

    protected function writePercent(int $row, int|float $rasio, string $kolom = self::KOLOM_PERSENTASE): void
    {
        $this->writer->writePercent($row, $rasio, $kolom);
    }

    /**
     * Rasio terhadap pendapatan, dengan guard pembagian nol.
     */
    protected function rasioKePendapatan(int|float $nilai): float
    {
        $pendapatan = $this->data->pendapatan();

        if ($pendapatan == 0.0) {
            return 0.0;
        }

        return (float) $nilai / $pendapatan;
    }

    /**
     * Baris subtotal/total: tebal dengan border atas.
     */
    protected function styleTotal(int $row, string $sampaiKolom = self::KOLOM_NILAI): void
    {
        $this->writer->styleTotal($row, $sampaiKolom);
    }

    /**
     * Catatan keterangan kecil (italic) di bawah tabel.
     */
    protected function writeNote(int $row, string $teks, string $sampaiKolom = self::KOLOM_NILAI): void
    {
        $this->writer->writeNote($row, $teks, $sampaiKolom);
    }

    /**
     * Finishing: lebar kolom, freeze pane di bawah header tabel, dan page setup.
     *
     * @param  array<int, string>  $kolomNilai
     */
    protected function finish(
        array $kolomNilai = [self::KOLOM_NILAI],
        string $orientasi = LaporanSheetWriter::ORIENTASI_PORTRAIT,
        int $lebarKolomUraian = 56,
    ): void {
        $this->writer->finish($kolomNilai, $orientasi, $lebarKolomUraian);
    }
}
