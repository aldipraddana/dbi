<?php

namespace App\Filament\Resources\LaporanKeuanganHistoryResource\Pages;

use App\Filament\Resources\LaporanKeuanganHistoryResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Illuminate\Http\RedirectResponse;

class ViewLaporanKeuanganHistory extends ViewRecord
{
    protected static string $resource = LaporanKeuanganHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cetakUlang')
                ->label('Cetak Ulang')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function (): RedirectResponse {
                    $record = $this->getRecord();

                    return redirect()->away(
                        route('laporan.keuangan.print', [
                            'jenis' => $record->jenis_laporan,
                            'tanggal_mulai' => $record->tanggal_mulai->format('Y-m-d'),
                            'tanggal_akhir' => $record->tanggal_akhir->format('Y-m-d'),
                        ])
                    );
                }),
        ];
    }
}
