<html>
    <head>
        <title>Laporan Penyewa Tanah Kas Kelurahan</title>
        <style>
            body{
                font-size: 10px;
            }
            th{
                background-color: #ff9100;
                padding: 8px;
                text-align: left;
                border: 1px solid #000;
            }
            tbody tr td{
                padding: 8px;
                border: 1px solid #000;
                text-align: center;
            }
        </style>
    </head>
    @php
        $years = [2025, 2026, 2027, 2028, 2029];
    @endphp
    <body>
        <h3>Daftar Penyewa Tanah Kas Kelurahan</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="3">Nama</th>
                    <th colspan="2" rowspan="2">Alamat</th>
                    <th rowspan="3">Persil</th>
                    <th rowspan="3">Kelas</th>
                    <th rowspan="3">Luas(m<sup>2</sup>)</th>
                    <th rowspan="2">Keterangan</th>
                    <th colspan="{{ count($years) * 3 }}" style="text-align: center;">Total Pembayaran</th>
                </tr>
                <tr>
                    @foreach ($years as $item)
                        <th colspan="3" style="text-align: center;">{{ $item }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th>DHKS</th>
                    <th>RT</th>
                    <th>RW</th>
                    <th>Digunakan</th>
                    @foreach ($years as $item)
                        <th>Tagihan</th>
                        <th>Tanggal</th>
                        <th>Bayar</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if ($persewaan->count() > 0)
                    @foreach ($persewaan as $index => $item)
                        <tr>
                            <td>{{ $item->transaction_number }}</td>
                            <td>{{ $item->tenant_name ?? '-' }}</td>
                            <td>{{ $item->asset_location_rt ?? '-' }}</td>
                            <td>{{ $item->asset_location_rw ?? '-' }}</td>
                            <td>{{ $item->asset_lot ?? '-' }}</td>
                            <td>{{ $item->asset_class ?? '-' }}</td>
                            <td>{{ $item->asset_size ?? '-' }}</td>
                            <td>{{ $usageTypes[$item->asset_usage_type_id] ?? '-' }}</td>
                            @foreach ($years as $year)
                                @php
                                    $yearlyPayments = $item->transactionBills()->whereYear('created_at', $year)->get();
                                    $totalTagihan = $yearlyPayments->sum('amount');
                                    $totalBayar = $item->transactionPayments()->whereYear('created_at', $year)->sum('nominal');
                                    $tanggalBayar = $item->transactionPayments()->whereYear('created_at', $year)->pluck('created_at')->first() ? \Carbon\Carbon::parse($item->transactionPayments()->whereYear('created_at', $year)->pluck('created_at')->first())->format('d-m-Y') : '-';
                                @endphp
                                <td>Rp{{ number_format($totalTagihan) }}</td>
                                <td>{{ $tanggalBayar }}</td>
                                <td>Rp{{ number_format($totalBayar) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ 9 + (count($years) * 3) }}" class="text-center">Tidak ada data penyewa tanah kas kelurahan.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </body>
</html>