<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\KendaraanResource\Pages;
use App\Models\Kendaraan;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KendaraanResource extends Resource
{
    public static ?string $label = UserMenuConstant::MENU_KENDARAAN;
    protected static ?string $model = Kendaraan::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_KENDARAAN;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Kendaraan')
                    ->schema([
                        Forms\Components\TextInput::make('no_polisi')
                            ->label('No. Polisi / Plat Nomor')
                            ->required()
                            ->placeholder('Masukkan nomor polisi')
                            ->extraInputAttributes([
                                'onInput' => "this.value = this.value.toUpperCase()"
                            ])
                            ->unique(ignorable: fn (?Kendaraan $record) => $record)
                            ->maxLength(20),
                        Forms\Components\Select::make('jenis_kendaraan')
                            ->label('Jenis Kendaraan')
                            ->required()
                            ->placeholder('Pilih jenis kendaraan')
                            ->options([
                                'Motor' => 'Motor',
                                'Mobil' => 'Mobil',
                                'Truk' => 'Truk',
                                'Bus' => 'Bus',
                                'Sepeda' => 'Sepeda',
                                'Lainnya' => 'Lainnya',
                            ]),
                        Forms\Components\TextInput::make('merk')
                            ->label('Merk')
                            ->placeholder('Masukkan merk kendaraan')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('model_tipe')
                            ->label('Model/Tipe')
                            ->placeholder('Masukkan model/tipe')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('tahun_pembuatan')
                            ->label('Tahun Pembuatan')
                            ->placeholder('Contoh: 2023')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y') + 1)
                            ->maxLength(4),
                        Forms\Components\TextInput::make('warna')
                            ->label('Warna')
                            ->placeholder('Masukkan warna kendaraan')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('no_rangka')
                            ->label('No. Rangka')
                            ->placeholder('Masukkan nomor rangka')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('no_mesin')
                            ->label('No. Mesin')
                            ->placeholder('Masukkan nomor mesin')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('kapasitas')
                            ->label('Kapasitas')
                            ->placeholder('Contoh: 1500cc / 5 orang')
                            ->maxLength(50),
                    ])
                    ->columns(2),
                Section::make('Kepemilikan & Penugasan')
                    ->schema([
                        Forms\Components\Select::make('status_kepemilikan')
                            ->label('Status Kepemilikan')
                            ->placeholder('Pilih status kepemilikan')
                            ->options([
                                'Milik Sendiri' => 'Milik Sendiri',
                                'Sewa' => 'Sewa',
                            ]),
                        Forms\Components\Select::make('karyawan_id')
                            ->label('Ditugaskan ke Karyawan')
                            ->placeholder('Pilih karyawan')
                            ->relationship('karyawan', 'nama_lengkap', fn ($query) => $query->where('status_aktif', true))
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('tanggal_berlaku_stnk')
                            ->label('Tanggal Berlaku STNK')
                            ->placeholder('Pilih tanggal')
                            ->native(false)
                            ->displayFormat('d F Y'),
                        Forms\Components\DatePicker::make('tanggal_berlaku_pajak')
                            ->label('Tanggal Berlaku Pajak')
                            ->placeholder('Pilih tanggal')
                            ->native(false)
                            ->displayFormat('d F Y'),
                    ])
                    ->columns(2),
                Section::make('Kondisi ')
                    ->schema([
                        Forms\Components\Select::make('kondisi')
                            ->label('Kondisi')
                            ->required()
                            ->default('Baik')
                            ->options([
                                'Baik' => 'Baik',
                                'Rusak Ringan' => 'Rusak Ringan',
                                'Rusak Berat' => 'Rusak Berat',
                            ]),
                        // Forms\Components\FileUpload::make('foto_kendaraan')
                        //     ->label('Foto Kendaraan')
                        //     ->image()
                        //     ->directory('kendaraan/foto')
                        //     ->imageEditor()
                        //     ->imageResizeMode('cover')
                        //     ->panelAspectRatio('16:9'),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->placeholder('Masukkan catatan')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Informasi Sistem')
                    ->schema([
                        Forms\Components\Select::make('created_by')
                            ->relationship('creator', 'name')
                            ->label('Dibuat Oleh')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Dibuat Pada')
                            ->displayFormat('d F Y H:i:s')
                            ->native(false)
                            ->disabled(),
                        Forms\Components\Select::make('updated_by')
                            ->relationship('updater', 'name')
                            ->label('Terakhir Diperbarui Oleh')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->displayFormat('d F Y H:i:s')
                            ->native(false)
                            ->disabled(),
                        Forms\Components\Select::make('deleted_by')
                            ->relationship('deleter', 'name')
                            ->label('Dihapus Oleh')
                            ->visible(fn (Kendaraan $record) => $record->trashed())
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('deleted_at')
                            ->label('Dihapus Pada')
                            ->displayFormat('d F Y H:i:s')
                            ->native(false)
                            ->visible(fn (Kendaraan $record) => $record->trashed())
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\ImageColumn::make('foto_kendaraan')
                //     ->label('Foto')
                //     ->size(40)
                //     ->square(),
                Tables\Columns\TextColumn::make('no_polisi')
                    ->label('No. Polisi')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('jenis_kendaraan')
                    ->label('Jenis')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('merk')
                    ->label('Merk')
                    ->sortable(),
                Tables\Columns\TextColumn::make('model_tipe')
                    ->label('Model/Tipe')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tahun_pembuatan')
                    ->label('Tahun')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('warna')
                    ->label('Warna')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status_kepemilikan')
                    ->label('Kepemilikan')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state) => $state === 'Milik Sendiri' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('kondisi')
                    ->label('Kondisi')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Baik' => 'success',
                        'Rusak Ringan' => 'warning',
                        'Rusak Berat' => 'danger',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('karyawan.nama_lengkap')
                    ->label('Ditugaskan ke')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('tanggal_berlaku_stnk')
                    ->label('STNK Berlaku')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tanggal_berlaku_pajak')
                    ->label('Pajak Berlaku')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('jenis_kendaraan')
                    ->label('Jenis Kendaraan')
                    ->options([
                        'Motor' => 'Motor',
                        'Mobil' => 'Mobil',
                        'Truk' => 'Truk',
                        'Bus' => 'Bus',
                        'Sepeda' => 'Sepeda',
                        'Lainnya' => 'Lainnya',
                    ]),
                Tables\Filters\SelectFilter::make('kondisi')
                    ->label('Kondisi')
                    ->options([
                        'Baik' => 'Baik',
                        'Rusak Ringan' => 'Rusak Ringan',
                        'Rusak Berat' => 'Rusak Berat',
                    ]),
                Tables\Filters\SelectFilter::make('status_kepemilikan')
                    ->label('Status Kepemilikan')
                    ->options([
                        'Milik Sendiri' => 'Milik Sendiri',
                        'Sewa' => 'Sewa',
                    ]),
                Tables\Filters\SelectFilter::make('karyawan_id')
                    ->label('Ditugaskan ke')
                    ->relationship('karyawan', 'nama_lengkap'),
            ])
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKendaraans::route('/'),
            'create' => Pages\CreateKendaraan::route('/create'),
            'edit' => Pages\EditKendaraan::route('/{record}/edit'),
        ];
    }
}
