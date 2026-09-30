<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\DanaMasukResource\Pages;
use App\Models\Client;
use App\Models\DanaMasuk;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class DanaMasukResource extends Resource
{
    protected static ?string $model = DanaMasuk::class;
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_DANA_MASUK;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Dana Masuk')
                    ->schema([
                        TextInput::make('nomor')
                            ->label('Nomor')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('tanggal_masuk')
                            ->label('Tanggal Masuk')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d F Y'),
                        TextInput::make('no_invoice')
                            ->label('No. Invoice')
                            ->required()
                            ->maxLength(255),
                        Select::make('customer')
                            ->label('Client / Customer')
                            ->required()
                            ->getSearchResultsUsing(fn(string $search) => Client::where('nama_client', 'like', "%{$search}%")
                                ->limit(10)->get()->mapWithKeys(fn($c) => [$c->id => $c->nama_client]))
                            ->searchable()
                            ->preload(false),
                        TextInput::make('po')
                            ->label('No. PO')
                            ->maxLength(255),
                        TextInput::make('item')
                            ->label('Item')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('jumlah_dana_masuk')
                            ->label('Jumlah Dana Masuk')
                            ->required()
                            ->prefix('Rp')
                            ->mask(\Filament\Support\RawJs::make('$money($input)'))
                            ->dehydrateStateUsing(fn ($state): float => (float) preg_replace('/\D/', '', (string) $state)),
                        Select::make('tipe_pembayaran')
                            ->label('Tipe Pembayaran')
                            ->required()
                            ->options([
                                'tunai' => 'Tunai',
                                'transfer' => 'Transfer',
                            ]),
                        Select::make('status_pembayaran')
                            ->label('Status Pembayaran')
                            ->required()
                            ->options([
                                'dp' => 'DP',
                                'lunas' => 'Lunas',
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor')
                    ->label('Nomor')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('no_invoice')
                    ->label('No. Invoice')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('customer')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('po')
                    ->label('No. PO')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('item')
                    ->label('Item')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jumlah_dana_masuk')
                    ->label('Jumlah Dana Masuk')
                    ->sortable()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float) $state, 2, ',', '.')),
                TextColumn::make('tipe_pembayaran')
                    ->label('Tipe Pembayaran')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'tunai' => 'info',
                        'transfer' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'dp' => 'DP',
                        'lunas' => 'Lunas',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'dp' => 'warning',
                        'lunas' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe_pembayaran')
                    ->label('Tipe Pembayaran')
                    ->options([
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                    ]),
                Tables\Filters\SelectFilter::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->options([
                        'dp' => 'DP',
                        'lunas' => 'Lunas',
                    ]),
            ])
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDanaMasuks::route('/'),
            'create' => Pages\CreateDanaMasuk::route('/create'),
            'edit' => Pages\EditDanaMasuk::route('/{record}/edit'),
        ];
    }
}
