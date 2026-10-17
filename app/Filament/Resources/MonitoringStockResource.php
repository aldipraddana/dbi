<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\MonitoringStockResource\Pages;
use App\Models\Client;
use App\Models\MonitoringStock;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MonitoringStockResource extends Resource
{
    public static ?string $label = UserMenuConstant::MENU_MONITORING_STOCK;

    protected static ?string $model = MonitoringStock::class;
    protected static ?string $navigationGroup = 'Stock';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_MONITORING_STOCK;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int $navigationSort = 7;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok_saat_ini')
                    ->label('Stok Saat Ini')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pm')
                    ->label('PM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_client')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Client')
                    ->options(fn () => Client::query()->pluck('nama_client', 'id'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->options(fn () => Supplier::query()->pluck('nama_supplier', 'id'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('stok_tersedia')
                    ->label('Stok tersedia saja')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('stok_saat_ini', '>', 0)),
            ])
            ->filtersFormColumns(2)
            ->defaultSort('nama_barang', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonitoringStocks::route('/'),
        ];
    }
}
