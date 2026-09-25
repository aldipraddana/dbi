<?php

use App\Exports\LaporanKeuanganExport;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/generate-gmail', [FrontendController::class, 'generateGmail'])->name('generate.gmail');

Route::middleware('auth')->group(function () {
    Route::get('bill-history/{id}', [RequestController::class, 'getBillHistory'])->name('bill-history');
    Route::get('bukti-kas-masuk/{id}', [RequestController::class, 'getBuktiKasMasuk'])->name('bukti.kas.masuk');
    Route::get('/redirect-bukti-kas-masuk/{id}', function ($id) {
        return view('filament.forms.components.redirect-bukti-kas-masuk', compact('id'));
    })->name('redirect.bukti.kas.masuk');
    Route::get('bukti-penagihan/{id}', [RequestController::class, 'getBuktiPenagihan'])->name('bukti.penagihan');
    Route::get('laporan-sewa', [RequestController::class, 'getLaporanSewa'])->name('laporan.sewa');
    Route::get('export-transactions', [RequestController::class, 'exportTransaction'])->name('export.transaction');
    Route::get('export-wifi-bill', [RequestController::class, 'exportWifiBill'])->name('export.wifi-bill');
    Route::get('import1', [RequestController::class, 'import1'])->name('import1');
    Route::post('import1', [RequestController::class, 'saveImport1'])->name('import1.save');
    Route::get('laporan-keuangan/print', [RequestController::class, 'printLaporanKeuangan'])->name('laporan.keuangan.print');
    Route::get('laporan-keuangan/download-excel', function (\Illuminate\Http\Request $request): StreamedResponse {
        $validated = $request->validate([
            'jenis' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date',
        ]);

        $export = new LaporanKeuanganExport(
            $validated['jenis'],
            $validated['tanggal_mulai'],
            $validated['tanggal_akhir']
        );

        return $export->download();
    })->name('laporan.keuangan.download-excel');
});
