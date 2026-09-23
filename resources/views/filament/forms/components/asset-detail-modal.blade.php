@php
use App\Models\Assets;
@endphp
<strong>Aset Utama</strong>
<div class="mb-4 p-4 bg-gray-50 rounded shadow">
    <div class="mb-1"><span class="font-semibold text-gray-600">Nomor Aset:</span> <span class="text-gray-800">{{ $record->asset_number }}</span></div>
    <div class="mb-1"><span class="font-semibold text-gray-600">Lokasi:</span> <span class="text-gray-800">{{ $record->location_detail }} (RT: {{ $record->location_rt }}, RW: {{ $record->location_rw }}, Padukuhan: {{ $record->location_padukuhan }})</span></div>
    <div class="mb-1"><span class="font-semibold text-gray-600">Kelas:</span> <span class="text-gray-800">{{ $record->class }}</span></div>
    <div class="mb-1"><span class="font-semibold text-gray-600">Luas:</span> <span class="text-gray-800">{{ $record->size }} m²</span></div>
    <div class="mb-1"><span class="font-semibold text-gray-600">Status:</span> <span class="text-gray-800">{{ $record->status?->name }}</span></div>
    <div class="mb-1"><span class="font-semibold text-gray-600">Harga Sewa:</span> <span class="text-gray-800">Rp{{ number_format($record->rental_price) }}</span></div>
</div>
<strong>Pecahan Aset</strong>
@php
    $fractions = Assets::where('parent_id', $record->id)
        ->where('deleted_at', null)
        ->get();
@endphp
@if ($fractions->isNotEmpty())
<table class="w-full text-sm text-left text-gray-500">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
            <th scope="col" class="px-4 py-2 text-sm font-medium">Nomor Aset</th>
            <th scope="col" class="px-4 py-2 text-sm font-medium">Luas</th>
            <th scope="col" class="px-4 py-2 text-sm font-medium">Penggunaan</th>
            <th scope="col" class="px-4 py-2 text-sm font-medium">Harga Sewa (dalam 1 tahun)</th>
            <th scope="col" class="px-4 py-2 text-sm font-medium">Status Sewa</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($fractions as $fraction)
            <tr class="bg-white border-b">
                <td class="px-4 py-2 text-sm font-medium">{{ $fraction->asset_number }}</td>
                <td class="px-4 py-2 text-sm font-medium">{{ $fraction->size }} m<sup>2</sup></td>
                <td class="px-4 py-2 text-sm font-medium">{{ $fraction->usageType->name ?? '' }}</td>
                <td class="px-4 py-2 text-sm font-medium">Rp{{ number_format($fraction->rental_price) }}</td>
                <td class="px-4 py-2 text-sm font-medium">{{ $fraction->status?->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@else
    <p class="text-gray-500">Tidak ada pecahan untuk aset ini.</p>
@endif