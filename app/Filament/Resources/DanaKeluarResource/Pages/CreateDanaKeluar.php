<?php

namespace App\Filament\Resources\DanaKeluarResource\Pages;

use App\Enums\DanaKeluarJenis;
use App\Enums\KategoriGaji;
use App\Enums\TipePekerja;
use App\Filament\Resources\DanaKeluarResource\Resource;
use App\Models\DanaKeluar;
use Filament\Resources\Pages\CreateRecord;

class CreateDanaKeluar extends CreateRecord
{
    protected static string $resource = DanaKeluarResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $jenis = $data['jenis'] ?? null;

        // Reset field yang tidak relevan berdasarkan jenis
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

        // Hitung ulang total di server
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

    protected function afterCreate(): void
    {
        if ($this->record->jenis === DanaKeluarJenis::GAJI && $this->record->kategori_gaji === KategoriGaji::GAJI_BULANAN) {
            $this->prosesPotonganKasBon($this->record);
        }
    }

    private function prosesPotonganKasBon(DanaKeluar $gaji): void
    {
        $potongan = (float) $gaji->potongan_kas_bon;
        if ($potongan <= 0) {
            return;
        }

        if ($gaji->tipe_pekerja === TipePekerja::HARIAN && $gaji->karyawan_id) {
            $kasBons = DanaKeluar::where('jenis', DanaKeluarJenis::GAJI)
                ->where('kategori_gaji', KategoriGaji::KAS_BON)
                ->where('karyawan_id', $gaji->karyawan_id)
                ->where('tipe_pekerja', TipePekerja::HARIAN)
                ->where('total', '>', 0)
                ->orderBy('tanggal')
                ->get();

            foreach ($kasBons as $kasBon) {
                if ($potongan <= 0) {
                    break;
                }
                $bayar = min($potongan, $kasBon->total);
                $kasBon->decrement('total', $bayar);
                $potongan -= $bayar;
            }
        } elseif ($gaji->tipe_pekerja === TipePekerja::BORONGAN && $gaji->nama_pemborong) {
            $kasBons = DanaKeluar::where('jenis', DanaKeluarJenis::GAJI)
                ->where('kategori_gaji', KategoriGaji::KAS_BON)
                ->where('tipe_pekerja', TipePekerja::BORONGAN)
                ->whereRaw('LOWER(nama_pemborong) = ?', [strtolower($gaji->nama_pemborong)])
                ->where('total', '>', 0)
                ->orderBy('tanggal')
                ->get();

            foreach ($kasBons as $kasBon) {
                if ($potongan <= 0) {
                    break;
                }
                $bayar = min($potongan, $kasBon->total);
                $kasBon->decrement('total', $bayar);
                $potongan -= $bayar;
            }
        }
    }
}
