<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\PenerimaanResource\Pages;
use App\Models\Penerimaan;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PenerimaanResource extends Resource
{
    public static ?string $label = UserMenuConstant::MENU_PENERIMAAN;
    protected static ?string $model = Penerimaan::class;
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_PENERIMAAN;
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Penerimaan')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y'),
                        Forms\Components\TextInput::make('nama_klien_proyek')
                            ->label('Nama Klien / Proyek')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('jenis_layanan')
                            ->label('Jenis Layanan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nomor_po')
                            ->label('Nomor PO')
                            ->default(fn () => \App\Models\Penerimaan::getNextNomorPO())
                            ->readonly(),
                        Forms\Components\TextInput::make('nilai_kontrak')
                            ->label('Nilai Kontrak (IDR)')
                            ->afterStateUpdated(function (callable $set, $state) {
                                $numericValue = preg_replace('/[^\d]/', '', $state);
                                $set('nilai_kontrak', $numericValue);
                            })
                            ->mask(\Filament\Support\RawJs::make('$money($input)'))
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\Select::make('termin_status')
                            ->label('Termin / Status')
                            ->required()
                            ->options(Penerimaan::getTerminStatusOptions())
                            ->preload(),
                        Forms\Components\TextInput::make('penerimaan_masuk')
                            ->label('Penerimaan Masuk (IDR)')
                            ->afterStateUpdated(function (callable $set, $state) {
                                $numericValue = preg_replace('/[^\d]/', '', $state);
                                $set('penerimaan_masuk', $numericValue);
                            })
                            ->mask(\Filament\Support\RawJs::make('$money($input)'))
                            ->prefix('Rp')
                            ->required(),
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
                Tables\Columns\TextColumn::make('nama_klien_proyek')
                    ->label('Nama Klien / Proyek')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_layanan')
                    ->label('Jenis Layanan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nomor_po')
                    ->label('Nomor PO')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nilai_kontrak')
                    ->label('Nilai Kontrak')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('termin_status')
                    ->label('Termin / Status')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('penerimaan_masuk')
                    ->label('Penerimaan Masuk')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
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
                Tables\Filters\SelectFilter::make('termin_status')
                    ->label('Termin / Status')
                    ->options(Penerimaan::getTerminStatusOptions()),
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
            'index' => Pages\ListPenerimaan::route('/'),
            'create' => Pages\CreatePenerimaan::route('/create'),
            'edit' => Pages\EditPenerimaan::route('/{record}/edit'),
        ];
    }
}
