<?php
if ($menu == 'transaction') {
    $route = route('export.transaction');
}elseif ($menu == 'tagihan wifi') {
    $route = route('export.wifi-bill');
}
?>
<style>
    .table--wiget-order-export td {
        padding-right: 10px;
    }
</style>
@if (isset($month))
<div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <div style="overflow-x: auto">
    <b class="text-lg font-semibold mb-4">Rekap Total Tagihan {{ date('F Y', strtotime($month)) }}</b>
        <table style="width:300px;margin-top:13px;">
            <tr>
                <td class="text-left pr-6">Jumlah Transaksi </td>
                <td>:</td>
                <td class="text-left">{{ $totalTransactions }}</td>
            </tr>
            <tr>
                <td class="text-left pr-6">Total Tagihan </td>
                <td>:</td>
                <td class="text-left">Rp {{ number_format($totalTagihan,0,',','.') }}</td>
            </tr>
            <tr>
                <td class="text-left pr-6">Total Dengan PPN </td>
                <td>:</td>
                <td class="text-left">Rp {{ number_format($totalPPN,0,',','.') }}</td>
            </tr>
        </table>
    </div>
</div>
@endif
<div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <b class="text-lg font-semibold mb-4">Export Data</b>
    <form action="{{ $route }}" class="form--order-export pt-4"  method="GET" target="_blank">
        <div style="overflow-x: auto;">
            <table style="width:830px" class="table--wiget-order-export">
                <tr>
                    <td>
                        <label for="po_date_1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Awal</label>
                        <input type="date" name="po_date_1" autocomplete="off" id="po_date_1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Pilih Tanggal Awal" />
                    </td>
                    <td>
                        <label for="po_date_2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Akhir</label>
                        <input type="date" name="po_date_2" autocomplete="off" id="po_date_2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Pilih Tanggal Akhir" />
                    </td>
                    @if ($menu == 'tagihan wifi')
                        <td>
                            <label for="wifi_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Pembayaran</label>
                            <select name="tipe_pembayaran" id="wifi_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">Pilih Semua</option>
                                <option value="titip">Titip</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </td>
                    @endif
                    <td style="width: 10%;padding-right:0;">
                        <button type="submit" class="text-sm font-semibold" style="background-color: #28a745;color: white;padding: 7px 20px;border-radius: .5rem;border: none;margin-top: 30px;width:100%">
                            Export
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </form>
    <br>
    <i class="text-sm">*)Kosongi range tanggal untuk melakukan export semua data</i>
</div>