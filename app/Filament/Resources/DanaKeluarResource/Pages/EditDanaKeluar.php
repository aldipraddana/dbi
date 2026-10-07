<?php

namespace App\Filament\Resources\DanaKeluarResource\Pages;

use App\Enums\DanaKeluarJenis;
use App\Enums\KategoriGaji;
use App\Filament\Resources\DanaKeluarResource;
use Filament\Resources\Pages\EditRecord;

class EditDanaKeluar extends EditRecord
{
    protected static string $resource = DanaKeluarResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $jenis = $data['jenis'] ?? null;

        if ($jenis !== DanaKeluarJenis::BAHAN_BAKU->value) {
            $data['supplier_id'] = null;
        }
        if ($jenis !== DanaKeluarJenis::OPERASIONAL->value) {
            $data['kendaraan_id'] = null;
            $data['karyawan_id'] = null;
            $data['keperluan'] = null;
        }
        if ($jenis !== DanaKeluarJenis::GAJI->value) {
            $data['kategori_gaji'] = null;
            $data['tipe_pekerja'] = null;
            $data['nama_proyek'] = null;
            $data['nama_mandor'] = null;
            $data['nama_pemborong'] = null;
            $data['nama_pekerjaan'] = null;
            $data['keterangan'] = null;
        }
        if (! in_array($jenis, [DanaKeluarJenis::BIAYA_KANTOR->value, DanaKeluarJenis::BIAYA_LAIN->value])) {
            $data['item'] = null;
        }
        if (! in_array($jenis, [DanaKeluarJenis::BIAYA_KANTOR->value, DanaKeluarJenis::BIAYA_LAIN->value, DanaKeluarJenis::OPERASIONAL->value])) {
            $data['qty'] = null;
        }

        // Hitung ulang total
        if ($jenis === DanaKeluarJenis::BAHAN_BAKU->value) {
            $data['total'] = collect($data['items'] ?? [])->sum(fn($item) => (float) ($item['subtotal'] ?? 0));
        } elseif (in_array($jenis, [DanaKeluarJenis::BIAYA_KANTOR->value, DanaKeluarJenis::BIAYA_LAIN->value])) {
            $data['total'] = (float) ($data['qty'] ?? 0) * (float) ($data['nominal'] ?? 0);
        } elseif ($jenis === DanaKeluarJenis::GAJI->value && $data['kategori_gaji'] === KategoriGaji::GAJI_BULANAN->value) {
            $data['total'] = max(0, (float) ($data['nominal'] ?? 0) - (float) ($data['potongan_kas_bon'] ?? 0));
        } else {
            $data['total'] = (float) ($data['nominal'] ?? 0);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncTotalBahanBaku();
    }

    private function syncTotalBahanBaku(): void
    {
        $record = $this->record;

        if ($record->jenis === \App\Enums\DanaKeluarJenis::BAHAN_BAKU) {
            $record->update(['total' => $record->items()->sum('subtotal')]);
        }
    }
}
