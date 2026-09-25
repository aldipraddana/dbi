<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\PengeluaranResource\Pages;
use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengeluaranResource extends Resource
{
    public static ?string $label = UserMenuConstant::MENU_EXPENSE;
    protected static ?string $model = Pengeluaran::class;
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_EXPENSE;
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Pengeluaran')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y'),
                        Forms\Components\Select::make('kategori_pengeluaran_id')
                            ->label('Kategori Pengeluaran')
                            ->required()
                            ->relationship('kategori', 'nama')
                            ->options(function () {
                                return KategoriPengeluaran::where('is_active', true)
                                    ->orderBy('nama')
                                    ->pluck('nama', 'id');
                            })
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('biaya')
                            ->label('Biaya (IDR)')
                            ->mask(\Filament\Support\RawJs::make('$money($input)'))
                            ->prefix('Rp')
                            ->afterStateUpdated(function (callable $set, $state) {
                                $numericValue = preg_replace('/[^\d]/', '', $state);
                                $set('biaya', $numericValue);
                            })
                            ->required(),
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi / Keperluan')
                            ->required()
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 2,
                                'lg' => 2,
                            ]),
                        Forms\Components\TextInput::make('penerima_vendor')
                            ->label('Penerima / Vendor')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('metode')
                            ->label('Metode Pembayaran')
                            ->required()
                            ->options([
                                'Tunai' => 'Tunai',
                                'Transfer' => 'Transfer',
                                'Debit' => 'Debit',
                                'Kredit' => 'Kredit',
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
                            ->label('Terakhir Diperbarui Oleh')
                            ->disabled(),
                        Forms\Components\DatePicker::make('updated_at')
                            ->label('Terakhir Diperbarui Pada Tanggal')
                            ->displayFormat('d F Y H:i:s')
                            ->native(false)
                            ->disabled(true),
                    ])
                    ->columns(2)
                    ->visibleOn('edit'),
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
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Kategori Pengeluaran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('biaya')
                    ->label('Biaya')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi / Keperluan')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('penerima_vendor')
                    ->label('Penerima / Vendor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('metode')
                    ->label('Metode Pembayaran')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal')
                    ->label('Tanggal')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('kategori_pengeluaran_id')
                    ->label('Kategori Pengeluaran')
                    ->relationship('kategori', 'nama'),
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
            'index' => Pages\ListPengeluaran::route('/'),
            'create' => Pages\CreatePengeluaran::route('/create'),
            'edit' => Pages\EditPengeluaran::route('/{record}/edit'),
        ];
    }
}
