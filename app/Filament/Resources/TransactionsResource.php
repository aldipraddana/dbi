<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Enums\PaymentStatusEnum;
use App\Filament\Resources\TransactionsResource\Pages;
use App\Models\Products;
use App\Models\Transactions;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionsResource extends Resource
{
    public static ?string $label = UserMenuConstant::MENU_TRANSACTION;
    protected static ?string $model = Transactions::class;
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationIcon = 'heroicon-m-folder-arrow-down';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_TRANSACTION;
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Transaksi')
                    ->schema([
                        Forms\Components\TextInput::make('transaction_number')
                            ->label('Nomor Transaksi')
                            ->required()
                            ->readOnly()
                            ->default(function () {
                                return generateSequentialNumberAsset(Transactions::class, 'BGJ', 'transaction_number');
                            })
                            ->maxLength(100),
                        Forms\Components\DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->default(Date::now())
                            ->displayFormat('d F Y')
                            ->placeholder('Pilih tanggal')
                            ->required(),
                        Forms\Components\TextInput::make('customer')
                            ->label('Pelanggan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('no_handphone')
                            ->label('No Handphone')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('Alamat')
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                PaymentStatusEnum::Paid->value => 'Lunas',
                                PaymentStatusEnum::Partial->value => 'Lunas Sebagian',
                                PaymentStatusEnum::Unpaid->value => 'Belum Lunas',
                                PaymentStatusEnum::Cancelled->value => 'Dibatalkan',
                            ])
                            ->label('Status Pembayaran')
                            ->placeholder('Pilih Status Pembayaran')
                            ->required(),
                        Section::make('Produk')
                        ->schema([
                            Forms\Components\Repeater::make('productTransactions')
                                ->hiddenLabel()
                                ->relationship('productTransactions')
                                ->schema([
                                    Forms\Components\TextInput::make('row_number')
                                        ->label('No.')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->formatStateUsing(function (Forms\Components\TextInput $component, Get $get): int {
                                            preg_match('/productTransactions\.([^\.]+)\.row_number$/', $component->getStatePath(), $matches);

                                            $items = $get('../../productTransactions') ?? [];
                                            $keys = array_map('strval', array_keys($items));
                                            $position = array_search($matches[1] ?? null, $keys, true);

                                            return ($position === false ? 0 : $position) + 1;
                                        })
                                        ->columnSpan([
                                            'sm' => 4,
                                            'md' => 1,
                                            'lg' => 1,
                                        ]),
                                    Forms\Components\TextInput::make('serial_number')
                                        ->label('Nomor Seri')
                                        ->disabled(true)
                                        ->columnSpan([
                                            'sm' => 4,
                                            'md' => 1,
                                            'lg' => 1,
                                        ])
                                        ->formatStateUsing(function ($state, $get) {
                                            $productId = $get('product_id');
                                            if ($productId) {
                                                $product = \App\Models\Products::find($productId);
                                                return $product?->serial_number ?? 'Akan muncul setelah memilih produk';
                                            }
                                            return 'Akan muncul setelah memilih produk';
                                        })
                                        ->maxLength(255),   
                                    Forms\Components\Select::make('product_id')
                                        ->options(function ($record) {
                                            $query = Products::select(
                                                'id',
                                                DB::raw("CONCAT(COALESCE(name, ''), ' ', COALESCE(color_memory, ''), ' - ', COALESCE(serial_number, '')) AS display_name")
                                            );
                                            $query->where(function ($q) use ($record) {
                                                $q->where('is_active', true);
                                                if ($record) {
                                                    $q->orWhere('id', $record->product_id);
                                                }
                                            });

                                            $items = $query->get()->mapWithKeys(function ($p) {
                                                $label = trim((string) ($p->display_name ?? ''));
                                                return [$p->id => $label];
                                            })->filter()->toArray();

                                            return $items;
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->columnSpan([
                                            'sm' => 4,
                                            'md' => 2,
                                            'lg' => 2,
                                        ])
                                        ->live()
                                        ->label('Produk')
                                        ->afterStateUpdated(function (Set $set, $get, $state) {
                                            // produk terpilih sebelumnya selain ini
                                            $allProduct = $get('../../productTransactions');
                                            $allProductSame = array_filter($allProduct, function ($item) use ($state) {
                                                return $item['product_id'] == $state;
                                            });
                                            if ($allProductSame && count($allProductSame) > 1) {
                                                $set('product_id', null);
                                                \Filament\Notifications\Notification::make()
                                                    ->title('Produk sudah dipilih sebelumnya. Silakan pilih produk lain.')
                                                    ->danger()
                                                    ->send();
                                                return;
                                            }

                                            $product = Products::find($state);
                                            if ($product) {
                                                $set('serial_number', $product->serial_number);
                                                $set('quantity', 1);
                                            } else {
                                                $set('serial_number', null);
                                            }
                                        })
                                        ->placeholder('Pilih Produk')
                                        ->required(),
                                    Forms\Components\TextInput::make('quantity')
                                        ->label('Qty')
                                        ->required()
                                        ->live(3000)
                                        ->afterStateUpdated(function (Set $set, $get, $state) {
                                            $quantity = (int) $state;
                                            $price = (int) preg_replace('/\D/', '', $get('price'));
                                            if ($quantity > 0) {
                                                $total = $quantity * $price;
                                                $set('subtotal', $total);
                                            }
                                        })
                                        ->columnSpan([
                                            'sm' => 4,
                                            'md' => 1,
                                            'lg' => 1,
                                        ])
                                        ->numeric(),
                                    Forms\Components\TextInput::make('price')
                                        ->label('Harga')
                                        ->mask(\Filament\Support\RawJs::make('$money($input)'))
                                        ->extraAttributes(['class' => 'custom-cost-price'])
                                        ->prefix('Rp')
                                        ->live(3000)
                                        ->afterStateUpdated(function (Set $set, $get, $state) {
                                            $quantity = (int) $get('quantity');
                                            $price = (int) preg_replace('/\D/', '', $state);
                                            if ($quantity > 0) {
                                                $total = $quantity * $price;
                                                $set('subtotal', $total);
                                            }
                                        })
                                        ->columnSpan([
                                            'sm' => 4,
                                            'md' => 2,
                                            'lg' => 2,
                                        ])
                                        ->required(),
                                    Forms\Components\TextInput::make('subtotal')
                                        ->label('Total')
                                        ->mask(\Filament\Support\RawJs::make('$money($input)'))
                                        ->extraAttributes(['class' => 'custom-cost-price'])
                                        ->prefix('Rp')
                                        ->readOnly()
                                        ->columnSpan([
                                            'sm' => 4,
                                            'md' => 3,
                                            'lg' => 3,
                                        ])
                                        ->required(),
                                ])
                                ->addActionLabel('Tambah Produk')
                                ->columns(5)
                        ])
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
                if (! $user->isAdmin()) {
                    $query->where('created_by', $user->id);
                }
                return $query;
            })
            ->columns([
                Tables\Columns\TextColumn::make('transaction_number')
                    ->label('Nomor Transaksi')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Pembayaran')
                    ->formatStateUsing(fn (?string $state): string => PaymentStatusEnum::from((string) $state)->translation())
                    ->badge()
                    ->color(fn (?string $state): string => PaymentStatusEnum::from((string) $state)->color())
                    ->icon(fn (?string $state): string => PaymentStatusEnum::from((string) $state)->icon())
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->where(function ($q) use ($search): void {
                            collect(PaymentStatusEnum::cases())
                                ->each(function (PaymentStatusEnum $paymentStatus) use ($q, $search): void {
                                    if (Str::contains(haystack: $search, needles: $paymentStatus->translation(), ignoreCase: true) === true) {
                                        $q->orWhere('payment_status', $paymentStatus->value);
                                    }
                                });
                        });
                    }),
                Tables\Columns\TextColumn::make('customer')
                    ->label('Nama Pelanggan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_handphone')
                    ->label('Nomor Handphone')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('updater.name')
                    ->label('Diperbarui Oleh')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('bukti_kas_masuk')
                    ->label('Bukti Pembayaran')
                    ->icon('heroicon-o-document-text')
                    ->url(fn ($record) => route('bukti.kas.masuk', ['id' => $record->id]))
                    ->openUrlInNewTab(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransactions::route('/create'),
            'edit' => Pages\EditTransactions::route('/{record}/edit'),
        ];
    }

}
