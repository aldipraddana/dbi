<?php

declare(strict_types=1);

namespace App\Filament\Resources\LaporanKeuanganResource\Pages;

use App\Enums\JenisLaporanEnum;
use App\Filament\Resources\LaporanKeuanganResource;
use App\Models\LaporanKeuanganHistory;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GenerateLaporanKeuangan extends Page
{
    protected static string $resource = LaporanKeuanganResource::class;

    protected static string $view = 'filament.resources.laporan-keuangan-resource.pages.generate-laporan-keuangan';

    protected static ?string $title = 'Laporan Keuangan';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return static::getResource()::form($form->statePath('data'));
    }

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateExcel')
                ->label('Generate Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {   // jangan pakai ": void" karena ada return redirect()
                    $this->validateForm();
                    $data = $this->form->getState();
                    $this->saveHistory($data, 'excel');

                    return redirect()->route(
                        'laporan.keuangan.download-excel',
                        $this->buildReportParams($data),
                    );
                }),
            Action::make('printPdf')
                ->label('Print / PDF')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function (): void {
                    $this->validateForm();
                    $data = $this->form->getState();
                    $params = $this->buildReportParams($data);

                    if ($params['jenis'] === JenisLaporanEnum::Semua->value) {
                        // TODO: dukung Print/PDF untuk jenis "Semua Jenis Laporan"
                        Notification::make()
                            ->title('Print / PDF untuk "Semua Jenis Laporan" belum didukung.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->saveHistory($data, 'pdf');

                    $url = route('laporan.keuangan.print', $params);

                    $this->js('window.open(' . json_encode($url) . ", '_blank')");
                }),
        ];
    }

    /**
     * Parameter request yang dipakai action Excel maupun Print / PDF.
     *
     * @param  array<string, mixed>  $data
     * @return array{jenis: string, tanggal_mulai: string, tanggal_akhir: string}
     */
    protected function buildReportParams(array $data): array
    {
        $jenis = $data['jenis_laporan'];

        return [
            'jenis' => $jenis instanceof BackedEnum
                ? (string) $jenis->value
                : (string) $jenis,
            'tanggal_mulai' => Carbon::parse($data['tanggal_mulai'])->format('Y-m-d'),
            'tanggal_akhir' => Carbon::parse($data['tanggal_akhir'])->format('Y-m-d'),
        ];
    }

    protected function validateForm(): void
    {
        $data = $this->form->getState();

        if (empty($data['tanggal_mulai'])) {
            throw ValidationException::withMessages([
                'data.tanggal_mulai' => ['Tanggal Mulai wajib diisi.'],
            ]);
        }

        if (empty($data['tanggal_akhir'])) {
            throw ValidationException::withMessages([
                'data.tanggal_akhir' => ['Tanggal Akhir wajib diisi.'],
            ]);
        }

        if (empty($data['jenis_laporan'])) {
            throw ValidationException::withMessages([
                'data.jenis_laporan' => ['Jenis Laporan wajib dipilih.'],
            ]);
        }

        if ($data['tanggal_mulai'] > $data['tanggal_akhir']) {
            throw ValidationException::withMessages([
                'data.tanggal_akhir' => ['Tanggal Mulai tidak boleh lebih besar dari Tanggal Akhir.'],
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function saveHistory(array $data, string $format): void
    {
        LaporanKeuanganHistory::create([
            'user_id' => Auth::id(),
            'jenis_laporan' => $data['jenis_laporan'],
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_akhir' => $data['tanggal_akhir'],
            'format' => $format,
        ]);
    }
}
