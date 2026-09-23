@php
    $billLogs = $transactionBils ?? [];
@endphp
@if (!empty($billLogs))
<table class="w-full text-sm text-left text-gray-500">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
            <th scope="col" class="px-4 py-2 text-sm font-medium">Status Pembayaran</th>
            <th scope="col" class="px-4 py-2 text-sm font-medium">Nomor Tagihan</th>
            <th scope="col" class="px-4 py-2 text-sm font-medium">Jumlah Tagihan</th>
            <th scope="col" class="px-4 py-2 text-sm font-medium">Jumlah Tunggakan</th>
            <th scope="col" class="px-4 py-2 text-sm font-medium">Jumlah Denda</th>
            <th scope="col" class="px-4 py-2 text-sm font-medium">Dibayar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($billLogs ?? [] as $log)
            <tr class="bg-white border-b">
                <td class="px-4 py-2 text-sm font-medium">{{ $log->status }}</td>
                <td class="px-4 py-2 text-sm font-medium">{{ $log->bill_number }}</td>
                <td class="px-4 py-2 text-sm font-medium">Rp{{ number_format($log->amount) }}</td>
                <td class="px-4 py-2 text-sm font-medium">Rp{{ number_format($log->arrears_amount) }}</td>
                <td class="px-4 py-2 text-sm font-medium">Rp{{ number_format($log->penalty_amount) }}</td>
                <td class="px-4 py-2 text-sm font-medium">Rp{{ number_format($log->paid_amount) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="text-gray-500">Tidak ada riwayat tagihan untuk transaksi ini.</p>
@endif