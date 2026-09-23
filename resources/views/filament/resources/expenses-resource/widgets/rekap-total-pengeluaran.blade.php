<div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <div style="overflow-x: auto">
        <b class="text-lg font-semibold mb-4">Rekap Total {{ $titleRangeDate }}</b>
        <table style="width: 320px; margin-top: 13px;">
            <tr>
                <td class="text-left pr-6">Total Pemasukan</td>
                <td>:</td>
                <td class="text-left">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-left pr-6">Total Pengeluaran</td>
                <td>:</td>
                <td class="text-left">Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-left pr-6">Total Saldo Saat Ini</td>
                <td>:</td>
                <td class="text-left">Rp {{ number_format($totalBalance ?? 0, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</div>