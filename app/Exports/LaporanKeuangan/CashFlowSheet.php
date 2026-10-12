<?php

declare(strict_types=1);

namespace App\Exports\LaporanKeuangan;

use App\Enums\JenisLaporanEnum;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * Laporan arus kas periode laporan (basis kas).
 *
 * Saldo kas akhir dihitung dari saldo kas awal + kenaikan/penurunan kas,
 * sehingga sejalan dengan Kas di Neraca.
 */
class CashFlowSheet extends BaseLaporanSheet
{
    protected function jenisLaporan(): JenisLaporanEnum
    {
        return JenisLaporanEnum::CashFlow;
    }

    public function render(): void
    {
        $this->writeReportHeader();
        $this->writeTableHeader(['A' => 'Uraian', 'B' => 'Nilai (Rp)']);

        $row = self::FIRST_CONTENT_ROW;

        // ------------------------------------------------------ ARUS KAS OPERASIONAL
        $this->writeSection($row, 'ARUS KAS OPERASIONAL');
        $row++;

        $penerimaan = $this->data->pendapatan();
        $this->writeLabel($row, 'Penerimaan dari Pelanggan');
        $this->writeAmount($row, $penerimaan);
        $row++;

        $pembayaranPemasok = $this->data->pembayaranPemasok();
        $this->writeLabel($row, 'Pembayaran ke Pemasok');
        $this->writeAmount($row, -$pembayaranPemasok);
        $row++;

        $pembayaranGaji = $this->data->pembayaranGaji();
        $this->writeLabel($row, 'Pembayaran Gaji');
        $this->writeAmount($row, -$pembayaranGaji);
        $row++;

        $pembayaranBeban = $this->data->pembayaranBeban();
        $this->writeLabel($row, 'Pembayaran Beban Operasional dan Lainnya');
        $this->writeAmount($row, -$pembayaranBeban);
        $row++;

        $arusOperasional = $penerimaan - $pembayaranPemasok - $pembayaranGaji - $pembayaranBeban;
        $this->writeLabel($row, 'Total Arus Kas Operasional', tebal: true);
        $this->writeAmount($row, $arusOperasional, tebal: true);
        $this->styleTotal($row);
        $row += 2;

        // -------------------------------------------------------- ARUS KAS INVESTASI
        $this->writeSection($row, 'ARUS KAS INVESTASI');
        $row++;

        // TODO: belum ada data sumber
        $this->writeLabel($row, 'Pembelian / Pelepasan Aset Tetap');
        $this->writeAmount($row, 0.0);
        $row++;

        $arusInvestasi = 0.0;
        $this->writeLabel($row, 'Total Arus Kas Investasi', tebal: true);
        $this->writeAmount($row, $arusInvestasi, tebal: true);
        $this->styleTotal($row);
        $row += 2;

        // ------------------------------------------------------- ARUS KAS PENDANAAN
        $this->writeSection($row, 'ARUS KAS PENDANAAN');
        $row++;

        // TODO: belum ada data sumber
        $this->writeLabel($row, 'Setoran Modal / Pinjaman / Pembayaran Utang');
        $this->writeAmount($row, 0.0);
        $row++;

        $arusPendanaan = 0.0;
        $this->writeLabel($row, 'Total Arus Kas Pendanaan', tebal: true);
        $this->writeAmount($row, $arusPendanaan, tebal: true);
        $this->styleTotal($row);
        $row += 2;

        // -------------------------------------------------------- KENAIKAN KAS
        $kenaikanKas = $arusOperasional + $arusInvestasi + $arusPendanaan;
        $this->writeLabel($row, 'Kenaikan / (Penurunan) Kas Neto', tebal: true);
        $this->writeAmount($row, $kenaikanKas, tebal: true);
        $this->styleTotal($row);
        $row += 2;

        $saldoAwal = $this->data->saldoKasSebelumPeriode();
        $this->writeLabel($row, 'Saldo Kas Awal');
        $this->writeAmount($row, $saldoAwal);
        $row++;

        $this->writeLabel($row, 'Saldo Kas Akhir', tebal: true);
        $this->writeAmount($row, $saldoAwal + $kenaikanKas, tebal: true);
        $this->styleTotal($row);
        $row += 2;

        $this->writeNote(
            $row,
            'Catatan: saldo kas awal adalah kumulatif kas sebelum tanggal mulai laporan. '
            . 'Arus kas investasi dan pendanaan masih 0 karena belum ada data sumber.',
        );

        $this->finish(orientasi: PageSetup::ORIENTATION_LANDSCAPE);
    }
}
