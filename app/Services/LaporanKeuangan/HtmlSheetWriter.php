<?php

declare(strict_types=1);

namespace App\Services\LaporanKeuangan;

/**
 * Penulisan sheet laporan untuk halaman print.
 *
 * Merekam urutan baris yang sama persis dengan yang ditulis ke Excel
 * (PhpSpreadsheetSheetWriter), dalam bentuk array siap-render Blade.
 *
 * Nomor baris Excel ikut direkam, sehingga baris yang dilewati sheet
 * (mis. `$row += 2`) menjadi baris kosong dan jarak antar bagian di print
 * sama dengan tampilan Excel.
 */
class HtmlSheetWriter implements LaporanSheetWriter
{
    public const string TIPE_KOSONG = 'kosong';

    public const string TIPE_TABLE_HEADER = 'table_header';

    public const string TIPE_SECTION = 'section';

    public const string TIPE_BARIS = 'baris';

    public const string TIPE_NOTE = 'note';

    /**
     * Header laporan (baris 1-3), diisi oleh writeReportHeader().
     *
     * @var array{namaPerusahaan: string, judul: string, periode: string}
     */
    private array $header;

    /**
     * Data per nomor baris sheet; digabung karena satu baris bisa ditulis
     * bertahap (label -> nilai -> persentase -> styling total).
     *
     * @var array<int, array{
     *     tipe: string,
     *     kolom?: array<int, string>,
     *     teks?: string,
     *     label?: string,
     *     nilai?: float,
     *     persen?: float,
     *     tebal?: bool,
     *     total?: bool
     * }>
     */
    private array $baris = [];

    private int $kolom = 2;

    private string $orientasi = LaporanSheetWriter::ORIENTASI_PORTRAIT;

    public function __construct()
    {
        $this->header = ['namaPerusahaan' => '', 'judul' => '', 'periode' => ''];
    }

    public function writeReportHeader(string $namaPerusahaan, string $judul, string $periode): void
    {
        $this->header = [
            'namaPerusahaan' => $namaPerusahaan,
            'judul' => $judul,
            'periode' => $periode,
        ];
    }

    public function writeTableHeader(array $headers, int $row): void
    {
        $this->baris[$row] = [
            'tipe' => self::TIPE_TABLE_HEADER,
            'kolom' => array_values($headers),
        ];

        $this->kolom = max($this->kolom, count($headers));
    }

    public function writeSection(int $row, string $teks, string $sampaiKolom): void
    {
        $this->baris[$row] = [
            'tipe' => self::TIPE_SECTION,
            'teks' => $teks,
        ];

        $this->catatKolom($sampaiKolom);
    }

    public function writeLabel(int $row, string $teks, bool $tebal): void
    {
        $baris = $this->baris[$row] ?? ['tipe' => self::TIPE_BARIS];

        $baris['label'] = $teks;
        $baris['tebal'] = ($baris['tebal'] ?? false) || $tebal;

        $this->baris[$row] = $baris;

        $this->catatKolom(self::KOLOM_URAIAN);
    }

    public function writeAmount(int $row, int|float $nilai, bool $tebal, string $kolom): void
    {
        $baris = $this->baris[$row] ?? ['tipe' => self::TIPE_BARIS];

        $baris['nilai'] = (float) $nilai;
        $baris['tebal'] = ($baris['tebal'] ?? false) || $tebal;

        $this->baris[$row] = $baris;

        $this->catatKolom($kolom);
    }

    public function writePercent(int $row, int|float $rasio, string $kolom): void
    {
        $baris = $this->baris[$row] ?? ['tipe' => self::TIPE_BARIS];

        $baris['persen'] = (float) $rasio;

        $this->baris[$row] = $baris;

        $this->catatKolom($kolom);
    }

    public function styleTotal(int $row, string $sampaiKolom): void
    {
        $baris = $this->baris[$row] ?? ['tipe' => self::TIPE_BARIS];

        $baris['total'] = true;
        $baris['tebal'] = true;

        $this->baris[$row] = $baris;

        $this->catatKolom($sampaiKolom);
    }

    public function writeNote(int $row, string $teks, string $sampaiKolom): void
    {
        $this->baris[$row] = [
            'tipe' => self::TIPE_NOTE,
            'teks' => $teks,
        ];

        $this->catatKolom($sampaiKolom);
    }

    public function finish(array $kolomNilai, string $orientasi, int $lebarKolomUraian): void
    {
        $this->orientasi = $orientasi;

        foreach ($kolomNilai as $kolom) {
            $this->catatKolom($kolom);
        }
    }

    /**
     * @return array{namaPerusahaan: string, judul: string, periode: string}
     */
    public function header(): array
    {
        return $this->header;
    }

    public function orientasi(): string
    {
        return $this->orientasi;
    }

    /**
     * Jumlah kolom laporan (uraian + nilai [+ persentase]).
     */
    public function kolom(): int
    {
        return $this->kolom;
    }

    /**
     * Seluruh baris berurutan, termasuk baris kosong akibat nomor baris terlewat.
     *
     * @return array<int, array<string, mixed>>
     */
    public function baris(): array
    {
        return $this->barisLengkap();
    }

    /**
     * Baris dipecah menjadi tabel-tabel: satu <thead> per judul kolom,
     * sehingga header bisa diulang tiap halaman cetak.
     *
     * @return array<int, array{header: array<int, string>, baris: array<int, array<string, mixed>>}>
     */
    public function tabel(): array
    {
        /** @var list<array<int, string>> $headerTabel */
        $headerTabel = [];

        /** @var list<list<array<string, mixed>>> $barisTabel */
        $barisTabel = [];

        foreach ($this->barisLengkap() as $baris) {
            if ($baris['tipe'] === self::TIPE_TABLE_HEADER) {
                $headerTabel[] = $baris['kolom'] ?? [];
                $barisTabel[] = [];

                continue;
            }

            if ($barisTabel === []) {
                $headerTabel[] = [];
                $barisTabel[] = [];
            }

            $barisTabel[count($barisTabel) - 1][] = $baris;
        }

        $tabel = [];

        foreach ($headerTabel as $indeks => $header) {
            $tabel[] = ['header' => $header, 'baris' => $barisTabel[$indeks]];
        }

        return $tabel === [] ? [['header' => [], 'baris' => []]] : $tabel;
    }

    /**
     * @return array<int, array{
     *     tipe: string,
     *     kolom?: array<int, string>,
     *     teks?: string,
     *     label?: string,
     *     nilai?: float,
     *     persen?: float,
     *     tebal?: bool,
     *     total?: bool
     * }>
     */
    private function barisLengkap(): array
    {
        ksort($this->baris);

        $hasil = [];
        $sebelumnya = null;

        foreach ($this->baris as $nomor => $baris) {
            if ($sebelumnya !== null) {
                for ($kosong = $sebelumnya + 1; $kosong < $nomor; $kosong++) {
                    $hasil[] = ['tipe' => self::TIPE_KOSONG];
                }
            }

            $hasil[] = $baris;
            $sebelumnya = $nomor;
        }

        return $hasil;
    }

    /**
     * Catat jumlah kolom dari huruf kolom Excel (A = 1, B = 2, C = 3).
     */
    private function catatKolom(string $kolom): void
    {
        $this->kolom = max($this->kolom, strlen($kolom));
    }
}
