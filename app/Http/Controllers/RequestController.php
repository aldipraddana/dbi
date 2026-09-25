<?php

namespace App\Http\Controllers;

use App\Enums\JenisLaporanEnum;
use App\Models\Transactions;
use App\Services\LaporanKeuangan\LaporanKeuanganService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RequestController extends Controller
{
    public function getBuktiKasMasuk($id)
    {
        $transaction = Transactions::with('productTransactions', 'creator', 'updater')->find($id);
        return view('filament.forms.components.transaction-bill-bukti-kas-masuk', [
            'transaction' => $transaction,
        ]);
    }

    public function exportTransaction(Request $request) 
    {
        $allQueryParams = $request->all();
        $statusPembayaran = $allQueryParams['status'] ?? '';
        if (isset($allQueryParams['po_date_1']) && isset($allQueryParams['po_date_2'])) {
            $allQueryParams['po_date_1'] = date('Y-m-d H:i:s', strtotime($allQueryParams['po_date_1'].' 00:00:00'));
            $allQueryParams['po_date_2'] = date('Y-m-d H:i:s', strtotime($allQueryParams['po_date_2']. '23:59:59'));
            $dateFormated = date('d F Y', strtotime($allQueryParams['po_date_1'])). '-sd-' . date('d F Y', strtotime($allQueryParams['po_date_2']));
        }else{
            $dateFormated = 'Semua';
        }

        $title = 'Rekap-Penjualan-delta-bagus-Periode-'.$dateFormated.'-'.time();

        $header = [
            'No', // a
            'Nomor Transaksi', // b
            'Tanggal Transaksi', // c
            'Nama Pelanggan', // d
            'Nomor Handphone', // e
            'Alamat', // f
            'Tanggal', // g
            'No. Seri', // h
            'IMEI 1', // i
            'Nama Produk', // j
            'Tipe', // k
            'Harga Beli', // l
            'Harga Jual', // m
            'Nama Joki', // n
            'Nominal Joki', // o
            'Keuntungan (Harga Jual - (Harga Beli + Nominal Joki))', // p
            'Dibuat Oleh', // q
            'Dibuat Pada Tanggal', // r
            'Status Pembayaran', // s
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Rekap Penjualan Delta Bagus Interior Periode '.$dateFormated);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('A1:S1');

        $sheet->fromArray($header, null, 'A2');

        $sheet->getColumnDimension('A')->setWidth(7);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(40);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(30);
        $sheet->getColumnDimension('M')->setWidth(30);
        $sheet->getColumnDimension('N')->setWidth(30);
        $sheet->getColumnDimension('O')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(20);
        $sheet->getColumnDimension('Q')->setWidth(25);
        $sheet->getColumnDimension('R')->setWidth(20);
        $sheet->getColumnDimension('S')->setWidth(20);
        
        $sheet->getStyle('A1:S2')->getFill()->setFillType('solid')->getStartColor()->setARGB('cbd6ff');
        $sheet->getStyle('A1:S2')->getFont()->setBold(true);

        //body
        $transaction = Transactions::with('productTransactions.product', 'creator', 'updater')
        ->when(isset($allQueryParams['po_date_1']) && isset($allQueryParams['po_date_2']), function ($query) use ($allQueryParams) {
            $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($allQueryParams['po_date_1'])));
            $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($allQueryParams['po_date_2'])));
        })
        ->get();

        $totalHargaJual = 0;
        $toalHargaBeli = 0;
        $totalJoki = 0;
        $baris = 3;
        foreach ($transaction as $key => $value) {
            foreach ($value->productTransactions as $key2 => $value2) {
                $sheet->setCellValue('A'.$baris, $baris-2);
                $sheet->setCellValue('B'.$baris, $value->transaction_number);
                $sheet->setCellValue('C'.$baris, date('d F Y H:i:s', strtotime($value->transaction_date)));
                $sheet->setCellValue('D'.$baris, $value->customer);
                $sheet->setCellValue('E'.$baris, $value->no_handphone);
                $sheet->setCellValue('F'.$baris, $value->address);
                $sheet->setCellValue('G'.$baris, date('d F Y', strtotime($value2->created_at)));

                $sheet->setCellValueExplicit('H'.$baris, $value2->product->serial_number ?? '-', DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('I'.$baris, $value2->product->imei1 ?? '-', DataType::TYPE_STRING);

                $sheet->setCellValue('J'.$baris, $value2->product->name ?? '-');
                $sheet->setCellValue('K'.$baris, $value2->product->type ?? '-');
                $sheet->setCellValue('L'.$baris, 'Rp '.number_format($value2->product->price, 0, '.', ','));
                $sheet->setCellValue('M'.$baris, 'Rp '.number_format($value2->price, 0, '.', ','));
                $sheet->setCellValue('N'.$baris, $value2->product->joki_name ?? '-');
                if ($value2->product->joki) {
                    $sheet->setCellValue('O'.$baris, 'Rp '.number_format($value2->product->joki_nominal, 0, '.', ','));
                } else {
                    $sheet->setCellValue('O'.$baris, '-');
                }
                $keuntungan = $value2->price - ($value2->product->price + ($value2->product->joki ? $value2->product->joki_nominal : 0));
                $sheet->setCellValue('P'.$baris, 'Rp '.number_format($keuntungan, 0, '.', ','));
                $sheet->setCellValue('Q'.$baris, $value->creator->name ?? '-');
                $sheet->setCellValue('R'.$baris, date('d-m-Y H:i:s', strtotime($value->created_at)));
                $sheet->setCellValue('S'.$baris, $value->status);

                $totalHargaJual += $value2->price;
                $toalHargaBeli += $value2->product->price;
                if ($value2->product->joki) {
                    $totalJoki += $value2->product->joki_nominal;
                }

                if ($value->status == 'BELUM LUNAS') {
                    $sheet->getStyle('A'.$baris.':R'.$baris)->getFill()->setFillType('solid')->getStartColor()->setARGB('ffc0ad');
                }
                $baris++;
            }
        }
        $sheet->setCellValue('A'.$baris, 'Total');

        $sheet->setCellValue('L'.$baris, 'Rp '.number_format($toalHargaBeli, 0, '.', ','));
        $sheet->setCellValue('M'.$baris, 'Rp '.number_format($totalHargaJual, 0, '.', ','));
        $sheet->setCellValue('N'.$baris, 'Keuntungan');
        $sheet->mergeCells('N'.$baris.':O'.$baris);
        $sheet->setCellValue('P'.$baris, 'Rp '.number_format($totalHargaJual - ($toalHargaBeli+$totalJoki), 0, '.', ','));
        $sheet->mergeCells('P'.$baris.':S'.$baris);
        $sheet->getStyle('A'.$baris.':S'.$baris)->getFill()->setFillType('solid')->getStartColor()->setARGB('cbd6ff');
        $sheet->getStyle('A'.$baris.':S'.$baris)->getFont()->setBold(true);

        $sheet->getStyle('A1:S'.$baris)->getBorders()->getAllBorders()->setBorderStyle('thin');

        $writer = new Xlsx($spreadsheet);
                
        $filename = $title.'.xlsx';
        $writer->save($filename);

        return response()->download($filename)->deleteFileAfterSend(true);  
    }

    private function getRangeMonth($start, $end) {
        $start    = (new \DateTime($start))->modify('first day of this month');
        $end      = (new \DateTime($end))->modify('first day of next month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start, $interval, $end);

        $months = [];
        foreach ($period as $dt) {
            $months[] = $dt->format("M-Y");
        }
        unset($months[count($months)-1]);
        return $months;
    }

    public function exportWifiBill(Request $request) 
    {
        $allQueryParams = $request->all();
        $tipePembayaran = $allQueryParams['tipe_pembayaran'] ?? '';
        $rangeMonths = [];
        if (isset($allQueryParams['po_date_1']) && isset($allQueryParams['po_date_2'])) {
            $allQueryParams['po_date_1'] = date('Y-m-d H:i:s', strtotime($allQueryParams['po_date_1'].' 00:00:00'));
            $allQueryParams['po_date_2'] = date('Y-m-d H:i:s', strtotime($allQueryParams['po_date_2']. '23:59:59'));
            $dateFormated = date('d F Y', strtotime($allQueryParams['po_date_1'])). '-sd-' . date('d F Y', strtotime($allQueryParams['po_date_2']));
            $rangeMonths = $this->getRangeMonth($allQueryParams['po_date_1'] ?? date('Y-m-01'), $allQueryParams['po_date_2'] ?? date('Y-m-d'));
        }else{
            $dateFormated = 'Semua';
        }

        $title = 'Rekap-Tagihan-Wifi-Periode-'.$dateFormated.'-'.time();

        $header = [
            'No', // a
            'Pelanggan', // b
            'No Handphone', // c
            'Alamat', // d
            'Kecepatan Wifi', // e
            'Tagihan', // f
            'PPN 11%', // g
            'Tipe Pembayaran', // h
            'Dibuat Oleh', // i
            'Dibuat Pada Tanggal', // j
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Rekap Tagihan Wifi Periode '.$dateFormated);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet->mergeCells('A1:J1');

        $sheet->fromArray($header, null, 'A2');

        $sheet->getColumnDimension('A')->setWidth(7);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(35);


        $order = \App\Models\WifiOrders::with('creator', 'updater')
        ->when(isset($allQueryParams['po_date_1']) && isset($allQueryParams['po_date_2']), function ($query) use ($allQueryParams) {
            $query->whereDate('payment_date', '>=', date('Y-m-01', strtotime($allQueryParams['po_date_1'])));
            $query->whereDate('payment_date', '<=', date('Y-m-t', strtotime($allQueryParams['po_date_2'])));
        })
        ->when($tipePembayaran, function ($query) use ($tipePembayaran) {
            $query->where('payment_type', $tipePembayaran);
        })
        ->orderBy('payment_date', 'ASC')
        ->get();

        $baris = 3;
        $bulanSebelumnya = '';
        $totalBillBulan = 0;
        $totalPpnBulan = 0;
        
        foreach ($order as $key => $value) {
            $periodeBulan = date('F Y', strtotime($value->payment_date));
            if ($bulanSebelumnya != $periodeBulan) {
                if ($key != 0) {
                    $sheet->setCellValue('A'.$baris, 'Total');
                    $sheet->mergeCells('A'.$baris.':E'.$baris);
                    $sheet->setCellValue('F'.$baris, 'Rp '.number_format($totalBillBulan, 2, '.', ','));
                    $sheet->setCellValue('G'.$baris, 'Rp '.number_format($totalPpnBulan, 2, '.', ','));
                    $sheet->setCellValue('H'.$baris, 'Selisih Pajak');
                    $sheet->mergeCells('H'.$baris.':I'.$baris);
                    $keuntungan = $totalPpnBulan - $totalBillBulan;
                    $sheet->setCellValue('J'.$baris, 'Rp '.number_format($keuntungan, 2, '.', ','));
                    $sheet->getStyle('A'.$baris.':J'.$baris)->getFill()->setFillType('solid')->getStartColor()->setARGB('cbd6ff');
                    $sheet->getStyle('A'.$baris.':J'.$baris)->getFont()->setBold(true);
                    $baris++;
                }
                $sheet->setCellValue('A'.$baris, 'Bulan '.$periodeBulan);
                $sheet->mergeCells('A'.$baris.':J'.$baris);
                $sheet->getStyle('A'.$baris)->getFont()->setBold(true);
                $sheet->getStyle('A'.$baris.':J'.$baris)->getFill()->setFillType('solid')->getStartColor()->setARGB('deffe9');
                $bulanSebelumnya = $periodeBulan;
                $totalBillBulan = 0;
                $totalPpnBulan = 0;
                $baris++;
            }
        
            $sheet->setCellValue('A'.$baris, $baris-3);
            $sheet->setCellValue('B'.$baris, $value->customer);
            $sheet->setCellValue('C'.$baris, $value->no_handphone);
            $sheet->setCellValue('D'.$baris, $value->address);
            $sheet->setCellValue('E'.$baris, $value->wifi_speed);
            $sheet->setCellValue('F'.$baris, 'Rp '.number_format($value->bill, 2, '.', ','));
            $sheet->setCellValue('G'.$baris, 'Rp '.number_format($value->ppn, 2, '.', ','));
            $sheet->setCellValue('H'.$baris, match ($value->payment_type) {
                'titip' => 'Titip',
                'transfer' => 'Transfer Bank',
                default => '-',
            });
            $sheet->setCellValue('I'.$baris, $value->creator->name ?? '-');
            $sheet->setCellValue('J'.$baris, date('d-m-Y H:i:s', strtotime($value->payment_date)));

            $totalBillBulan += $value->bill;
            $totalPpnBulan += $value->ppn;

            $baris++;
        }

        $sheet->setCellValue('A'.$baris, 'Total');
        $sheet->mergeCells('A'.$baris.':E'.$baris);
        $sheet->setCellValue('F'.$baris, 'Rp '.number_format($totalBillBulan, 2, '.', ','));
        $sheet->setCellValue('G'.$baris, 'Rp '.number_format($totalPpnBulan, 2, '.', ','));
        $sheet->setCellValue('H'.$baris, 'Selisih Pajak');
        $sheet->mergeCells('H'.$baris.':I'.$baris);
        $keuntungan = $totalPpnBulan - $totalBillBulan;
        $sheet->setCellValue('J'.$baris, 'Rp '.number_format($keuntungan, 2, '.', ','));
        $sheet->getStyle('A'.$baris.':J'.$baris)->getFill()->setFillType('solid')->getStartColor()->setARGB('cbd6ff');
        $sheet->getStyle('A'.$baris.':J'.$baris)->getFont()->setBold(true);

        $baris++;
        $sheet->setCellValue('A'.$baris, 'Total Keseluruhan');
        $sheet->mergeCells('A'.$baris.':E'.$baris);
        $sheet->setCellValue('F'.$baris, 'Rp '.number_format($order->sum('bill'), 2, '.', ','));
        $sheet->setCellValue('G'.$baris, 'Rp '.number_format($order->sum('ppn'), 2, '.', ','));
        $sheet->setCellValue('H'.$baris, 'Selisih Pajak');
        $sheet->mergeCells('H'.$baris.':I'.$baris);
        $keuntungan = $order->sum('ppn')- $order->sum('bill');
        $sheet->setCellValue('J'.$baris, 'Rp '.number_format($keuntungan, 2, '.', ','));
        $sheet->getStyle('A'.$baris.':J'.$baris)->getFill()->setFillType('solid')->getStartColor()->setARGB('b6d7a8');
        $sheet->getStyle('A'.$baris.':J'.$baris)->getFont()->setBold(true);

        $sheet->getStyle('A1:J'.$baris)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle('A1:J2')->getFont()->setBold(true);
        $sheet->getStyle('A1:J2')->getFill()->setFillType('solid')->getStartColor()->setARGB('cbd6ff');
        
        /**
         * rekap 3 bulanan
         * Rekap 3bulanan
        total 3Bulan +pajak, sek di etung sek ono pajak,e, di x 60%, 
        Pajak = (hasil dari 60% di bagi 1,11)x11%
        BHP USO = (hasil dari 60% di bagi 1,11)*1,75%
        Net Diterima pihak kedua = (total 3bulan + pajak) - pajak - BHP USO

        Administrasi LDP ke WPOP (Taufan)
        Bruto = Hasil Net diterima pihak kedua
        PPh21 (NPWP) = Bruto x 2,5%
        Net Ditransfer = Bruto - PPh21 (NPWP)
         */
        if (count($rangeMonths) === 2) {
            $baris += 2;
            $rekapStartBaris = $baris;
            $titleMonths = implode(', ', $rangeMonths);
            $sheet->setCellValue('B'.$baris, 'Rekap Bulan '. $titleMonths);
            $sheet->mergeCells('B'.$baris.':D'.$baris);
            $sheet->getStyle('B'.$baris)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('B'.$baris.':D'.$baris)->getFill()->setFillType('solid')->getStartColor()->setARGB('85ff90');
            $baris++;
            $rangeMonths = array_map(function($month) {
                return explode('-', $month)[0];
            }, $rangeMonths);
            $titleMonths = implode(', ', $rangeMonths);
            $sheet->setCellValue('B'.$baris, 'Total '.$titleMonths);
            $sheet->setCellValue('D'.$baris, number_format($order->sum('ppn'), 2, '.', ','));
            $baris++;
            $sheet->setCellValue('B'.$baris, 'Total '.$titleMonths);
            $sheet->setCellValue('C'.$baris, '60%');
            $total60 = $order->sum('ppn')*60/100;
            $sheet->setCellValue('D'.$baris, number_format($total60, 2, '.', ','));
            $baris++;
            $sheet->setCellValue('B'.$baris, 'Pajak');
            $sheet->setCellValue('C'.$baris, '11%');
            $total11 = ($total60/1.11)*11/100;
            $sheet->setCellValue('D'.$baris, number_format($total11, 2, '.', ','));
            $baris++;
            $sheet->setCellValue('B'.$baris, 'BHP USO');
            $bhpuso = ($total60/1.11)*1.75/100;
            $sheet->setCellValue('D'.$baris, number_format($bhpuso, 2, '.', ','));
            $baris++;
            $sheet->setCellValue('B'.$baris, 'Nett diterima Pihak Kedua');
            $sheet->mergeCells('B'.$baris.':C'.$baris);
            $nett = $total60 - $total11 - $bhpuso;
            $sheet->setCellValue('D'.$baris, number_format($nett, 2, '.', ','));

            $sheet->getStyle('D'.$rekapStartBaris.':D'.$baris)->getAlignment()->setHorizontal('left');
            $sheet->getStyle('B'.$rekapStartBaris.':D'.$baris)->getBorders()->getAllBorders()->setBorderStyle('thin');
            $sheet->getStyle('B'.$rekapStartBaris.':D2')->getFont()->setBold(true);
            
            $baris +=2;
            $barisAwalNetDiTf = $baris;
            $sheet->setCellValue('B'.$baris, 'Administrasi LDP ke WPOP (Taufan)');
            $sheet->mergeCells('B'.$baris.':D'.$baris);
            $sheet->getStyle('B'.$baris)->getAlignment()->setHorizontal('center');
            $baris++;
            $sheet->setCellValue('B'.$baris, 'Bruto');
            $sheet->mergeCells('B'.$baris.':C'.$baris);
            $bruto = $nett;
            $sheet->setCellValue('D'.$baris, number_format($bruto, 2, '.', ','));
            $baris++;
            $sheet->setCellValue('B'.$baris, 'PPh 21(NPWP)');
            $sheet->mergeCells('B'.$baris.':C'.$baris);
            $pph =  $bruto*2.5/100;
            $sheet->setCellValue('D'.$baris, number_format($pph, 2, '.', ','));
            $baris++;
            $sheet->setCellValue('B'.$baris, 'Net Ditransfer');
            $sheet->mergeCells('B'.$baris.':C'.$baris);
            $netDiTf = $bruto - $pph;
            $sheet->setCellValue('D'.$baris, number_format($netDiTf, 2, '.', ','));

            $sheet->getStyle('D'.$barisAwalNetDiTf.':D'.$baris)->getAlignment()->setHorizontal('left');
            $sheet->getStyle('B'.$barisAwalNetDiTf.':D'.$baris)->getBorders()->getAllBorders()->setBorderStyle('thin');
            $sheet->getStyle('B'.$barisAwalNetDiTf.':D2')->getFont()->setBold(true);
            $sheet->getStyle('B'.($barisAwalNetDiTf+1).':D'.$baris)->getFill()->setFillType('solid')->getStartColor()->setARGB('8feda4');
        }
        
        $writer = new Xlsx($spreadsheet);
                
        $filename = $title.'.xlsx';
        $writer->save($filename);

        return response()->download($filename)->deleteFileAfterSend(true);  
    }

    public function printLaporanKeuangan(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $jenisLaporan = JenisLaporanEnum::tryFrom($validated['jenis']);

        if (!$jenisLaporan) {
            abort(400, 'Jenis laporan tidak valid');
        }

        $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
        $tanggalAkhir = Carbon::parse($validated['tanggal_akhir']);

        $service = app(LaporanKeuanganService::class);
        $headerData = $service->getHeaderData($jenisLaporan, $tanggalMulai, $tanggalAkhir);

        return view('laporan-keuangan.print', $headerData);
    }

    public function import1()
    {
        return view('import.import1');
    }

    public function saveImport1(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Skip header row
        unset($rows[0]);
        unset($rows[1]);
        unset($rows[2]);
        unset($rows[3]);


        foreach ($rows as $row) {
            $total = str_replace(['.', ','], '', $row[9]);
            if (strlen($total) == 3) {
                $total .= '000';
            }else {
                $total .= '00';
            }
            \App\Models\WifiCustomers::updateOrCreate(
                ['customer' => $row[5]], // Unique identifier nama pelanggan
                [
                    'address' => $row[6],
                    'no_handphone' => str_replace("'", '', $row[7]),
                    'wifi_speed' => $row[8],
                    'bill' => (int)$total - ((int)$total*11/100),
                    'ppn' => (int)$total,
                    'created_at' => date('Y-m-d H:i:s', strtotime($row[4])),
                    'created_by' => 1,
                    'updated_by' => 1
                ]
            );
        }

        return redirect()->back()->with('success', 'Data imported successfully.');
    }

    
}
