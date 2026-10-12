<?php

declare(strict_types=1);

namespace App\Exports\LaporanKeuangan;

use App\Enums\DanaKeluarJenis;
use App\Enums\JenisLaporanEnum;
use App\Enums\KategoriGaji;
use App\Enums\TipePekerja;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * Catatan atas Laporan Keuangan:
 * Bagian A - kebijakan akuntansi (teks), Bagian B - rincian pendukung.
 */
class CatatanKeuanganSheet extends BaseLaporanSheet
{
    protected function jenisLaporan(): JenisLaporanEnum
    {
        return JenisLaporanEnum::CatatanKeuangan;
    }

    public function render(): void
    {
        $this->writeReportHeader();

        $row = self::FIRST_CONTENT_ROW;

        // ------------------------------------------- BAGIAN A: KEBIJAKAN AKUNTANSI
        $this->writeSection($row, 'BAGIAN A - KEBIJAKAN AKUNTANSI');
        $row++;

        foreach ($this->kebijakanAkuntansi() as $kebijakan) {
            $this->writeLabel($row, $kebijakan);
            $row++;
        }

        $row++;

        // ------------------------------------------------- BAGIAN B: RINCIAN
        $this->writeSection($row, 'BAGIAN B - RINCIAN PENDUKUNG');
        $row++;

        $this->writeTableHeader(['A' => 'Keterangan', 'B' => 'Jumlah'], $row);
        $row += 1;

        $row = $this->tulisRincian($row, 'Pendapatan per Customer', $this->data->pendapatanPerCustomer(), 'Total Pendapatan');
        $row = $this->tulisRincian($row, 'Beban per Jenis', $this->bebanPerJenis(), 'Total Beban');
        $row = $this->tulisRincian($row, 'Gaji per Kategori', $this->gajiPerKategori(), 'Total Gaji');
        $row = $this->tulisRincian($row, 'Gaji per Tipe Pekerja', $this->gajiPerTipePekerja(), 'Total Gaji per Tipe Pekerja');
        $row = $this->tulisRincian($row, 'Pembelian Bahan Baku per Supplier', $this->data->bahanBakuPerSupplier(), 'Total Pembelian Bahan Baku');

        $this->writeNote($row, 'Seluruh rincian di atas diambil dari data Dana Masuk dan Dana Keluar pada periode laporan.');

        $this->finish(orientasi: PageSetup::ORIENTATION_LANDSCAPE, lebarKolomUraian: 64);
    }

    /**
     * Bagian A: kebijakan akuntansi laporan (teks).
     *
     * @return array<int, string>
     */
    private function kebijakanAkuntansi(): array
    {
        return [
            'Dasar Penyajian: laporan keuangan ini disusun dengan dasar kas (cash basis), '
                . 'yaitu kas diterima diakui sebagai pendapatan dan kas keluar diakui sebagai beban.',
            'Mata Uang: seluruh angka disajikan dalam Rupiah (Rp).',
            'Sumber Data: laporan disusun dari data Dana Masuk (kas masuk) dan Dana Keluar (kas keluar).',
            'Pendapatan: diakui saat dana diterima sesuai tanggal masuk, untuk status pembayaran yang '
                . 'menandakan dana sudah dibayar (lunas maupun uang muka / dp).',
            'Beban: diakui saat kas dibayarkan sesuai tanggal keluar, berdasarkan nilai total dana keluar '
                . '(sudah memperhitungkan potongan kas bon).',
            'Penyusun: laporan ini dibuat otomatis oleh sistem dan belum diaudit.',
        ];
    }

    /**
     * @return array<string, float>
     */
    private function bebanPerJenis(): array
    {
        $hasil = [];

        foreach ($this->data->pengeluaranPerJenis() as $jenis => $nilai) {
            $label = DanaKeluarJenis::tryFrom($jenis)?->label() ?? ucfirst($jenis);
            $hasil[$label] = $nilai;
        }

        return $hasil;
    }

    /**
     * @return array<string, float>
     */
    private function gajiPerKategori(): array
    {
        $hasil = [];

        foreach ($this->data->gajiPerKategori() as $kategori => $nilai) {
            $label = KategoriGaji::tryFrom($kategori)?->label()
                ?? ($kategori === 'tidak_ditentukan' ? 'Tanpa Kategori' : ucfirst($kategori));
            $hasil[$label] = $nilai;
        }

        return $hasil;
    }

    /**
     * @return array<string, float>
     */
    private function gajiPerTipePekerja(): array
    {
        $hasil = [];

        foreach ($this->data->gajiPerTipePekerja() as $tipe => $nilai) {
            $label = TipePekerja::tryFrom($tipe)?->label()
                ?? ($tipe === 'tidak_ditentukan' ? 'Tanpa Tipe Pekerja' : ucfirst($tipe));
            $hasil[$label] = $nilai;
        }

        return $hasil;
    }

    /**
     * @param  array<string, float>  $rincian
     */
    private function tulisRincian(int $row, string $judul, array $rincian, string $labelTotal): int
    {
        $this->writeSection($row, $judul);
        $row++;

        if ($rincian === []) {
            $this->writeLabel($row, 'Tidak ada data pada periode ini');
            $this->writeAmount($row, 0.0);
            $row++;
        } else {
            foreach ($rincian as $keterangan => $nilai) {
                $this->writeLabel($row, (string) $keterangan);
                $this->writeAmount($row, (float) $nilai);
                $row++;
            }
        }

        $this->writeLabel($row, $labelTotal, tebal: true);
        $this->writeAmount($row, array_sum($rincian), tebal: true);
        $this->styleTotal($row);
        $row += 2;

        return $row;
    }
}
