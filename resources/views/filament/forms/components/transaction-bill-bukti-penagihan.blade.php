<?php 
use App\Models\Assets;
use App\Models\Transactions;

$transaction = Transactions::find($transactionBill->transaction_id);
$assetDetail = Assets::find($transaction->asset_id);
?>
<html>
    <head>
        <title>Bukti Penagihan</title>
        <style>
            .border-bottom td {
                padding: 16px 8px;
                border-bottom: 1px solid #000000;
            }
            .border-all td {
                padding: 8px;
                border: 1px solid #000000;
            }
            .red {
                color: red;
                font-style: italic;
            }
        </style>
    </head>
    <body>
        <table class="border-bottom" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="width: 20%;border-right: 0; text-align: center;">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width: 100px">
                </td>
                <td style="text-align: center;border-left: 0;">
                    <h3>PEMERINTAH KABUPATEN SLEMAN</h3>
                    <h2>KAPANEWON MLATI</h2>
                    <h2>PEMERINTAH KELURAHAN SINDUADI</h2>
                    <p>Jalan Magelang KM 4,5 Rogoyudan, Sinduadi, Mlati, Sleman, 55284</p>
                    <p>Telepon: (0274) 558210, Faksimile (0274) 558210</p>
                </td>
            </tr>
        </table>
        <p style="text-align: right">Sinduadi, {{ $transactionBill->created_at->format('d F Y') }}</p>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 10%;">Nomor</td>
                <td style="width: 1%;">:</td>
                <td>{{ $transactionBill->bill_number }}</td>
            </tr>
            <tr>
                <td>Lamp.</td>
                <td>:</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Hal.</td>
                <td>: </td>
                <td>Penagihan Pembayaran Uang Sewa Tanah kas Kelurahan</td>
            </tr>
            <tr>
                <td colspan="3">
                    <br><br>
                </td>
            </tr>
            <tr>
                <td>Kepada</td>
                <td>: </td>
                <td></td>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="width: 50%;vertical-align: top;">
                    Bapak/Ibu {{ $transaction->tenant->name }} <br>
                    Padukuhan {{ $assetDetail->location_padukuhan }} <br>
                    Di Sinduadi
                </td>
                <td style="text-align: right; width: 50%;vertical-align: top;">
                    No. Sewa : {{$assetDetail->asset_number}} <br>
                </td>
            </tr>
        </table>

        <p>Dengan Hormat,</p>
        <p>Bersama ini kami beritahukan bahwa menurut pembukuan kami, Bapak/Ibu masih memiliki kewajiban yang belum dibayarkan yaitu Pembayaran Sewa Tanah Kas Kalurahan dengan rincian sebagai berikut :</p>
        <table class="border-all" style="width: 410px; border-collapse: collapse; margin-bottom: 20px;margin-top: 20px;margin-left: 130px;">
            <tr>
                <td style="width: 66%;">Sewa Tunggakan Tahun Sebelumnya</td>
                <td> Rp. {{ number_format($transactionBill->arrears_amount ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Sewa Tahun {{ date('Y') }}</td>
                <td> Rp. {{ number_format($transactionBill->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Jumlah Sewa Harus Dibayar</td>
                <td> Rp. {{ number_format(($transactionBill->amount + $transactionBill->arrears_amount + $transactionBill->penalty_amount), 0, ',', '.') }}</td>
            </tr>
        </table>
        <p>
            Berdasarkan kesepakatan yang telah disepakati sebelumnya antara pihak penyewa dengan Pemerintah Kalurahan Sinduadi terkait ketentuan sewa Tanah Kas Kalurahan (TKKal), maka dengan ini kami sangat berharap Bapak/Ibu segera menyelesaikan urusan pembayarannya. 
            <br><br>
            Demikian pemberitahuan ini kami sampaikan, atas perhatiannya kami ucapkan terima kasih
        </p>
        <table style="width: 100%; border-collapse: collapse; margin-top: 40px;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%;text-align: center;">
                    <p>Hormat Kami,</p>
                    <p>Lurah Sinduadi</p>
                    <br><br><br>
              </td>
            </tr>
        </table>
    </body>
</html>