<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\PengeluaranStockResource\Pages;
use App\Models\PengeluaranStock;
use App\Models\PengadaanStockDetail;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengeluaranStockResource extends Resource
{
    public static ?string $label = UserMenuConstant::MENU_PENGELUARAN_STOCK;
    protected static ?string $model = PengeluaranStock::class;
    protected static ?string $navigationGroup = 'Stock';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Pengeluaran')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'nama_client')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_keluar')
                            ->label('Tanggal Keluar')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y'),
                    ])
                    ->columns(2),
                Repeater::make('details')
                    ->label('List Barang Pengeluaran')
                    ->relationship('details')
                    ->schema([
                        Forms\Components\Select::make('pengadaan_stock_detail_id')
                            ->label('Nama Barang')
                            ->getSearchResultsUsing(function (string $search) {
                                return PengadaanStockDetail::with('pengadaanStock.client')
                                    ->whereHas('pengadaanStock', fn($q) => $q->where('qty', '>', 0))
                                    ->where(function ($q) use ($search) {
                                        $q->where('nama_barang', 'like', "%{$search}%")
                                            ->orWhere('tipe', 'like', "%{$search}%")
                                            ->orWhere('pm', 'like', "%{$search}%");
                                    })
                                    ->limit(10)
                                    ->get()
                                    ->mapWithKeys(fn($detail) => [
                                        $detail->id => sprintf('%s | Tersedia: %s', $detail->nama_barang, $detail->getAvailableQty()),
                                    ]);
                            })
                            ->searchable()
                            ->preload(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    $set('nama_barang', null);
                                    $set('tipe', null);
                                    $set('pm', null);
                                    $set('qty', null);
                                    return;
                                }
                                $detail = PengadaanStockDetail::with('pengadaanStock.client')->find($state);
                                if ($detail) {
                                    $set('nama_barang', $detail->nama_barang);
                                    $set('tipe', $detail->tipe);
                                    $set('pm', $detail->pm);
                                    $set('qty', null);
                                }
                            }),
                        TextInput::make('nama_barang')
                            ->label('Nama Barang')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('tipe')
                            ->label('Tipe')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('pm')
                            ->label('PM')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('qty')
                            ->label('Qty')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state === null || $state === '') {
                                    return;
                                }
                                $intState = (int) $state;
                                if ($intState <= 0) {
                                    return;
                                }
                                $detailId = $get('pengadaan_stock_detail_id');
                                if (! $detailId) {
                                    return;
                                }
                                $detail = PengadaanStockDetail::find($detailId);
                                if (! $detail) {
                                    return;
                                }
                                $available = $detail->getAvailableQty();
                                if ($intState > $available) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Stok tidak mencukupi')
                                        ->body("Stok tersedia hanya {$available}. Qty akan dikosongkan.")
                                        ->send();
                                    $set('qty', $available);
                                }
                            }),
                    ])
                    ->columns(5)
                    ->addActionLabel('Tambah Item')
                    ->columnSpanFull()
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
                Tables\Columns\TextColumn::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->date('d M Y')
                    ->sortable(),
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
            'index' => Pages\ListPengeluaranStocks::route('/'),
            'create' => Pages\CreatePengeluaranStock::route('/create'),
            'view' => Pages\ViewPengeluaranStock::route('/{record}'),
            'edit' => Pages\EditPengeluaranStock::route('/{record}/edit'),
        ];
    }
}
