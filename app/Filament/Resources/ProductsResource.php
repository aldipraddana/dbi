<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\ProductsResource\Pages;
use App\Models\Products;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProductsResource extends Resource
{
    public static ?string $label = UserMenuConstant::MENU_PRODUCT_MANAGEMENT;
    protected static ?string $model = Products::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_PRODUCT_MANAGEMENT;
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
                Section::make('Data Produk')
                    ->schema([
                        Forms\Components\TextInput::make('serial_number')
                            ->label('Nomor Seri')
                            ->required()
                            ->extraInputAttributes([ 'x-on:keydown.enter.prevent' => '$refs.imei1.focus()', ])
                            ->visible(fn () => Auth::user()->isAdmin())
                            ->placeholder('Masukkan nomor seri'),
                        Forms\Components\TextInput::make('marketplace')
                            ->required()
                            ->label('Marketplace')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('imei1')
                            ->label('IMEI')
                            ->visible(fn () => Auth::user()->isAdmin())
                            ->extraInputAttributes([ 
                                'x-on:keydown.enter.prevent' => '$refs.type.focus()', 
                                 'x-ref' => 'imei1',
                                ])
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->required()
                            ->extraInputAttributes(['x-ref' => 'type'])
                            ->label('Jenis')
                            ->options([
                                'Handphone' => 'Handphone',
                                'Tablet' => 'Tablet',
                                'Laptop' => 'Laptop',
                                'Aksesoris' => 'Aksesoris',
                                'Lainnya' => 'Lainnya',
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Nama Produk')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('color_memory')
                            ->label('Warna dan Kapasitas Memori')
                            ->placeholder(''),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Produk (Bisa Dibeli/Tidak)')
                            ->inline(false)
                            ->default(true)
                            ->visible(fn () => Auth::user()->isAdmin())
                            ->required(),
                        Forms\Components\Toggle::make('joki')
                            ->label('Layanan Joki')
                            ->inline(false)
                            ->default(false)
                            ->required()
                            ->visible(fn () => Auth::user()->isAdmin())
                            ->reactive(),
                        Forms\Components\TextInput::make('price')
                            ->label('Harga Beli')
                            ->mask(\Filament\Support\RawJs::make('$money($input)'))
                            ->extraAttributes(['class' => 'custom-cost-price'])
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('joki_name')
                            ->label('Nama Joki')
                            ->maxLength(255)
                            ->disabled(fn ($get) => $get('joki') === false)
                            ->visible(fn ($get) => $get('joki') === true),
                        Forms\Components\TextInput::make('joki_nominal')
                            ->label('Nominal Joki')
                            ->mask(\Filament\Support\RawJs::make('$money($input)'))
                            ->extraAttributes(['class' => 'custom-cost-price'])
                            ->prefix('Rp')
                            ->disabled(fn ($get) => $get('joki') === false)
                            ->visible(fn ($get) => $get('joki') === true),
                        Forms\Components\TextInput::make('date_of_entry')
                            ->label('Tanggal Masuk')
                            ->placeholder('Pilih tanggal masuk')
                            ->visible(fn () => Auth::user()->isAdmin())
                            ->type('date')
                            ->required(),
                        Forms\Components\TextInput::make('date_of_purchase')
                            ->label('Tanggal Pembelian')
                            ->placeholder('Pilih tanggal pembelian')
                            ->type('date'),
                        Forms\Components\TextInput::make('booking_number')
                            ->label('Nomor Pemesanan')
                            ->placeholder('Masukkan nomor pemesanan')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 2,
                                'lg' => 2,
                            ]),
                    ])
                    ->columns(2),
                Section::make('Informasi Tambahan')
                    ->schema([
                        Forms\Components\Select::make('created_by')
                            ->relationship('creator', 'name')
                            ->label('Dibuat Oleh')
                            ->disabled(),
                        Forms\Components\DatePicker::make('created_at')
                            ->label('Dibuat Pada Tanggal')
                            ->displayFormat('d F Y H:i:s')
                            ->native(false)
                            ->disabled(true),
                        Forms\Components\Select::make('updated_by')
                            ->relationship('updater', 'name')
                            ->label('Terakhir Diperbaru Oleh')
                            ->disabled(),
                        Forms\Components\DatePicker::make('updated_at')
                            ->label('Terakhir Diperbarui Pada Tanggal')
                            ->displayFormat('d F Y H:i:s')
                            ->native(false)
                            ->disabled(true),
                    ])
                    ->columns(2)
                    ->visibleOn('edit')
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();
                $query->when(! $user->isAdmin(), function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                });
                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->sortable()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Tidak Aktif')
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Nomor Seri')
                    ->sortable()
                    ->visible(fn () => Auth::user()->isAdmin())
                    ->searchable(),
                Tables\Columns\TextColumn::make('imei1')
                    ->label('Nomor IMEI')
                    ->sortable()
                    ->visible(fn () => Auth::user()->isAdmin())
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        $value =  $state;
                        if ($record->color_memory) {
                            $value .= ' ('.$record->color_memory.')';
                        }
                        return $value;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('marketplace')
                    ->label('Nama Marketplace')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->sortable()
                    ->searchable(),
                    Tables\Columns\TextColumn::make('joki_name')
                    ->label('Nama Joki')
                    ->sortable()
                    ->visible(fn () => Auth::user()->isAdmin())
                    ->searchable(),   
                    Tables\Columns\TextColumn::make('joki_nominal')
                    ->label('Nominal Joki')
                    ->sortable()
                    // ->visible(fn () => Auth::user()->isAdmin())
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_entry')
                    ->label('Tanggal Masuk')
                    ->date('d F Y')
                    ->visible(fn () => Auth::user()->isAdmin())
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_purchase')
                    ->label('Tanggal Pembelian')
                    ->date('d F Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('booking_number')
                    ->label('Nomor Pemesanan')
                    ->sortable()
                    ->visible(fn () => Auth::user()->isAdmin())
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status Produk')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ])
                    ->default(1),
                Tables\Filters\Filter::make('date_of_purchase')
                    ->label('Tanggal Pembelian')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Tanggal Awal'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Tanggal Akhir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_of_entry', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_of_entry', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProducts::route('/create'),
            'edit' => Pages\EditProducts::route('/{record}/edit'),
        ];
    }
}
