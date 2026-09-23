<?php 
?>
<html>
    <head>
        <title>Bukti Transaksi</title>
        <style>
            td{
                font-size: 12px !important;
            }
            body{
                font-family: sans-serif;
            }
            .padding td {
                padding: 5px 3px;
            }
            .red {
                color: red;
                font-style: italic;
            }
            .bordered {
                border-top: 1px solid #000000;
            }
            .bordered-bottom {
                border-bottom: 1px solid #000000;
            }
            td{
                vertical-align: top;
            }
            @media print {
                @page {
                    size: A5 landscape;
                    margin: 10mm;
                }
            }
        </style>
    </head>
    <body>
        <table class="padding" style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 20%;border-right: 0; text-align: center;vertical-align: middle;" rowspan="5">
                    <img src="{{ asset('img/logo.jpeg') }}" alt="Logo" style="width: 100px">
                </td>
                <td style="text-align: center;border-left: 0;" colspan="2">
                    <h3 style="text-align: center;font-size: 18px;">Bakul Gadget Jogja</h3>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="bordered" style="text-align: center">JI. Parangtritis Km.22 Busuran Donotirto Kretek Bantul, Yogyakarta, 55772</td>
            </tr>
            <tr>
                <td class="bordered" style="vertical-align: middle;"><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" viewBox="0 0 48 48">
<path fill="#f4511e" d="M36.683,43H11.317c-2.136,0-3.896-1.679-3.996-3.813l-1.272-27.14C6.022,11.477,6.477,11,7.048,11 h33.904c0.571,0,1.026,0.477,0.999,1.047l-1.272,27.14C40.579,41.321,38.819,43,36.683,43z"></path><path fill="#f4511e" d="M32.5,11.5h-2C30.5,7.364,27.584,4,24,4s-6.5,3.364-6.5,7.5h-2C15.5,6.262,19.313,2,24,2 S32.5,6.262,32.5,11.5z"></path><path fill="#fafafa" d="M24.248,25.688c-2.741-1.002-4.405-1.743-4.405-3.577c0-1.851,1.776-3.195,4.224-3.195 c1.685,0,3.159,0.66,3.888,1.052c0.124,0.067,0.474,0.277,0.672,0.41l0.13,0.087l0.958-1.558l-0.157-0.103 c-0.772-0.521-2.854-1.733-5.49-1.733c-3.459,0-6.067,2.166-6.067,5.039c0,3.257,2.983,4.347,5.615,5.309 c3.07,1.122,4.934,1.975,4.934,4.349c0,1.828-2.067,3.314-4.609,3.314c-2.864,0-5.326-2.105-5.349-2.125l-0.128-0.118l-1.046,1.542 l0.106,0.087c0.712,0.577,3.276,2.458,6.416,2.458c3.619,0,6.454-2.266,6.454-5.158C30.393,27.933,27.128,26.741,24.248,25.688z"></path>
</svg><span> : <a href="https://id.shp.ee/nigC2Sf">bakulgadgetjogja.official</a></span></td>
                <td class="bordered"><img src="{{ asset('img/instagram.png') }}" style ="width: 20px;" alt=""> : <a href="https://www.instagram.com/bakulgadget.jogja?igsh=MWhrZm5wbWFwNHF2NA==">bakulgadget.jogja</a></td>
            </tr>
            <tr>
                <td><img src="{{ asset('img/tokopedia.png') }}" style="width:20px" alt=""> : <a href="https://tk.tokopedia.com/ZSD6f8r9s/">bakulgadgetofficial</a></td>
                <td><img src="{{ asset('img/tiktok.png') }}" style="width:20px" alt=""> : <a href="https://www.tiktok.com/@bakulgadget.official?=ZS-906rbWKOu41&_r=1">bakulgadget.official</a></td>
            </tr>
            <tr>
                <td colspan="2" class="bordered" style="text-align: center"><img src="{{ asset('img/wa.png') }}" style="width: 15px" alt=""> &nbsp;Marketing : <a href="https://wa.me/6289601990776">0896-0199-0776</a> &nbsp;&nbsp;<img src="{{ asset('img/wa.png') }}" style="width: 15px" alt=""> &nbsp;Sales : <a href="https://wa.me/6285183370498">0851-8337-0498</a></td>
            </tr>
        </table>
        <table class="padding bordered" style="width: 100%;border-collapse:collapse">
            <tr>
                <td style="width: 20%">No. Tagihan</td>
                <td>: {{ $transaction->transaction_number }}</td>
                <td></td>
                <td style="width: 20%">Receipt Number</td>
                <td>: {{ $transaction->no_handphone }}</td>
            </tr>
            <tr>
                <td>Tanggal / Date</td>
                <td>: {{ date('d-M-Y', strtotime($transaction->transaction_date)) }}</td>
                <td></td>
                <td>Alamat / Address</td>
                <td>: {{ $transaction->address }}</td>
            </tr>
            <tr>
                <td>Kepada / For</td>
                <td>: {{ $transaction->customer }}</td>
                <td></td>
                <td>Tanggal Cetak</td>
                <td>: {{ date('d-M-Y H:i:s') }}</td>
            </tr>
        </table>
        <table class="padding bordered" style="width: 100%;border-collapse:collapse;">
            <tr class="">
                <td class="bordered-bottom" style="text-align:center;width: 5%">No</td>
                <td class="bordered-bottom" style="text-align:center;width: 25%">Nama Barang</td>
                <td class="bordered-bottom" style="text-align:center;width: 17%">IMEI / No Seri</td>
                <td class="bordered-bottom" style="text-align:center;width: 10%">Qty</td>
                <td class="bordered-bottom" style="text-align:center">Harga</td>
                <td class="bordered-bottom" style="text-align:center;width: 20%">Sub Total</td>
            </tr>
            @php
                $total = 0;
            @endphp
            @foreach ($transaction->productTransactions as $item)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}.</td>
                    <td style="text-align: center">{{ $item->product->name }} (  {{ $item->product->color_memory }})</td>
                    <td style="text-align: center">{{ $item->product->type == 'Handphone' ? $item->product->imei1 : $item->product->serial_number }}</td>
                    <td style="text-align: center">{{ $item->quantity }}</td>
                    <td style="text-align: center">Rp{{ number_format($item->price, 2, ',', '.') }}</td>
                    <td style="text-align: center">Rp{{ number_format($item->subtotal, 2, ',', '.') }}</td>
                </tr>
                @php
                    $total += $item->subtotal;
                @endphp
            @endforeach
            <tr>
                <td colspan="4" class="bordered" style="text-align: center;"></td>
                <td class="bordered" style="text-align: center;padding-top:10px">Total Keseluruhan</td>
                <td class="bordered" style="text-align: center;padding-top:10px">Rp{{ number_format($total, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="5" rowspan="2" style="text-align: left;padding-top: 15px;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Penerima,<br>
                </td>
                <td style="text-align: center;padding-top: 15px;">Hormat kami</td>
            </tr>
            <tr>
                <td colspan="5"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: left;padding-top:40px">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
                <td style="text-align: center;padding-top:40px">( {{ ucfirst($transaction->creator->name) }} )</td>
            </tr>
        </table>
    </body>
</html>

<script>
    // Print the page when it loads
    window.onload = function() {
        window.print();
    };
</script>