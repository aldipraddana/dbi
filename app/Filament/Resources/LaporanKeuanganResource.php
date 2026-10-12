<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Enums\JenisLaporanEnum;
use App\Filament\Resources\LaporanKeuanganResource\Pages;
use App\Models\LaporanKeuangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Validation\Rules\Enum as EnumRule;

class LaporanKeuanganResource extends Resource
{
    protected static ?string $model = LaporanKeuangan::class;

    protected static ?string $label = UserMenuConstant::MENU_LAPORAN_KEUANGAN;

    protected static ?string $pluralLabel = UserMenuConstant::MENU_LAPORAN_KEUANGAN;

    protected static ?string $slug = 'laporan-keuangan';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = UserMenuConstant::MENU_LAPORAN_KEUANGAN;

    protected static ?int $navigationSort = 1;

    /**
     * Model halaman ini tidak memiliki tabel, jadi jangan ikut global search.
     */
    protected static bool $isGloballySearchable = false;

    public static function form(Form $form): Form
    {
        return $form
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
                            ->options(JenisLaporanEnum::options())
                            ->rule(new EnumRule(JenisLaporanEnum::class)),
                    ]),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\GenerateLaporanKeuangan::route('/'),
        ];
    }
}
