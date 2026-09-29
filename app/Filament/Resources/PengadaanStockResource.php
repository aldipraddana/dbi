<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\PengadaanStockResource\Pages;
use App\Models\PengadaanStock;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengadaanStockResource extends Resource
{
    protected static ?string $model = PengadaanStock::class;
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_PENGADAAN_STOCK;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Pengadaan')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'nama_client')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_masuk')
                            ->label('Tanggal Masuk')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y'),
                        Forms\Components\Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship('supplier', 'nama_supplier')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(3),
                Repeater::make('details')
                    ->label('List Barang Pengadaan')
                    ->relationship('details')
                    ->schema([
                        Forms\Components\TextInput::make('nama_barang')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tipe')
                            ->label('Tipe')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('pm')
                            ->label('PM')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('qty')
                            ->label('Qty')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
                    ->addActionLabel('Tambah Item')
                    ->reorderable(false)
                    ->defaultItems(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.nama_client')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.nama_supplier')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('details_count')
                    ->label('Jumlah Item')
                    ->counts('details')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'nama_client')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'nama_supplier')
                    ->searchable()
                    ->preload(),
            ])
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPengadaanStocks::route('/'),
            'create' => Pages\CreatePengadaanStock::route('/create'),
            'view' => Pages\ViewPengadaanStock::route('/{record}'),
            'edit' => Pages\EditPengadaanStock::route('/{record}/edit'),
        ];
    }
}
