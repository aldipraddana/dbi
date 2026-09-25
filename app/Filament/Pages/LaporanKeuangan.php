<?php

namespace App\Filament\Pages;

use App\Constants\UserMenuConstant;
use App\Enums\JenisLaporanEnum;
use App\Models\LaporanKeuanganHistory;
use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LaporanKeuangan extends Page
{
    protected static ?string $label = UserMenuConstant::MENU_LAPORAN_KEUANGAN;
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_LAPORAN_KEUANGAN;
    protected static ?string $title = 'Laporan Keuangan';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.laporan-keuangan';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tanggal_mulai')
                                    ->label('Tanggal Mulai')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d F Y'),
                                DatePicker::make('tanggal_akhir')
                                    ->label('Tanggal Akhir')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d F Y')
                                    ->afterOrEqual('tanggal_mulai'),
                            ]),
                        Select::make('jenis_laporan')
                            ->label('Jenis Laporan')
                            ->required()
                            ->options(JenisLaporanEnum::options()),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateExcel')
                ->label('Generate Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function (): void {
                    $this->validateForm();
                    $data = $this->form->getState();
                    $this->saveHistory($data, 'excel');

                    $this->dispatch('download-file', [
                        'url' => route('laporan.keuangan.download-excel', [
                            'jenis' => $data['jenis_laporan'],
                            'tanggal_mulai' => $data['tanggal_mulai'],
                            'tanggal_akhir' => $data['tanggal_akhir'],
                        ]),
                    ]);
                }),
            Action::make('printPdf')
                ->label('Print / PDF')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function (): void {
                    $this->validateForm();
                    $data = $this->form->getState();
                    $this->saveHistory($data, 'pdf');

                    $this->dispatch('open-print-view', [
                        'url' => route('laporan.keuangan.print', [
                            'jenis' => $data['jenis_laporan'],
                            'tanggal_mulai' => $data['tanggal_mulai'],
                            'tanggal_akhir' => $data['tanggal_akhir'],
                        ]),
                    ]);
                }),
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
