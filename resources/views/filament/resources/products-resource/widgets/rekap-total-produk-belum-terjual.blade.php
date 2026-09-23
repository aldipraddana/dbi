<div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <div style="overflow-x: auto">
        <b class="text-lg font-semibold mb-4">Rekap Total</b>
        <table style="width: 320px; margin-top: 13px;">
            @if (Auth::user()->isAdmin())
                <tr>
                    <td class="text-left pr-6">Total Harga Produk</td>
                    <td>:</td>
                    <td class="text-left">Rp {{ number_format($totalPrice ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td class="text-left pr-6">Total Nominal Joki</td>
                <td>:</td>
                <td class="text-left">Rp {{ number_format($totalNominalJoki ?? 0, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</div>
