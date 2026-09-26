<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\KaryawanResource\Pages;
use App\Models\Karyawan;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KaryawanResource extends Resource
{
    public static ?string $label = UserMenuConstant::MENU_KARYAWAN;
    protected static ?string $model = Karyawan::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_KARYAWAN;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Karyawan')
                    ->schema([
                        Forms\Components\TextInput::make('nik')
                            ->label('NIK / No. Induk Karyawan')
                            ->required()
                            ->placeholder('Masukkan NIK')
                            ->unique(ignorable: fn (?Karyawan $record) => $record)
                            ->maxLength(50),
                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->placeholder('Masukkan nama lengkap')
                            ->extraInputAttributes([
                                'onInput' => "this.value = this.value.replace(/\b\w/g, c => c.toUpperCase())"
                            ])
                            ->maxLength(255),
                        Forms\Components\Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->placeholder('Pilih jenis kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ]),
                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->placeholder('Pilih tanggal lahir')
                            ->native(false)
                            ->displayFormat('d F Y'),
                        Forms\Components\TextInput::make('no_telepon')
                            ->label('No. Telepon')
                            ->placeholder('Masukkan nomor telepon')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->placeholder('Masukkan email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->placeholder('Masukkan alamat lengkap')
                            ->rows(2)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('kota')
                            ->label('Kota')
                            ->placeholder('Masukkan kota')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('no_ktp')
                            ->label('No. KTP')
                            ->placeholder('Masukkan nomor KTP')
                            ->maxLength(50),
                    ])
                    ->columns(2),
                Section::make('Data Pekerjaan')
                    ->schema([
                        Forms\Components\TextInput::make('jabatan')
                            ->label('Jabatan')
                            ->placeholder('Masukkan jabatan')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('departemen')
                            ->label('Departemen')
                            ->placeholder('Masukkan departemen')
                            ->maxLength(100),
                        Forms\Components\DatePicker::make('tanggal_masuk_kerja')
                            ->label('Tanggal Masuk Kerja')
                            ->placeholder('Pilih tanggal masuk kerja')
                            ->native(false)
                            ->displayFormat('d F Y'),
                        Forms\Components\Select::make('status_kepegawaian')
                            ->label('Status Kepegawaian')
                            ->placeholder('Pilih status')
                            ->options([
                                'Tetap' => 'Tetap',
                                'Kontrak' => 'Kontrak',
                                'Magang' => 'Magang',
                            ]),
                        Forms\Components\Toggle::make('status_aktif')
                            ->label('Status Aktif')
                            ->inline(false)
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
                // Section::make('Dokumen Pendukung')
                //     ->schema([
                //         Forms\Components\FileUpload::make('dokumen_pendukung')
                //             ->label('Dokumen Pendukung')
                //             ->directory('karyawan/dokumen')
                //             ->acceptedFileTypes([
                //                 'application/pdf',
                //                 'image/*',
                //                 'application/msword',
                //                 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                //             ])
                //             ->maxSize(10240)
                //             ->openable()
                //             ->downloadable(),
                //     ])
                //     ->collapsible(),
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
                            ->visible(fn (Karyawan $record) => $record->trashed())
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('deleted_at')
                            ->label('Dihapus Pada')
                            ->displayFormat('d F Y H:i:s')
                            ->native(false)
                            ->visible(fn (Karyawan $record) => $record->trashed())
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
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->sortable()
                    ->formatStateUsing(fn (string $state) => $state === 'L' ? 'L' : 'P')
                    ->badge()
                    ->color(fn (string $state) => $state === 'L' ? 'info' : 'danger'),
                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('departemen')
                    ->label('Departemen')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status_kepegawaian')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Tetap' => 'success',
                        'Kontrak' => 'warning',
                        'Magang' => 'gray',
                        default => 'secondary',
                    }),
                Tables\Columns\IconColumn::make('status_aktif')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_telepon')
                    ->label('No. Telepon')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kota')
                    ->label('Kota')
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
                Tables\Filters\SelectFilter::make('status_aktif')
                    ->label('Status Aktif')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ]),
                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
                Tables\Filters\SelectFilter::make('status_kepegawaian')
                    ->label('Status Kepegawaian')
                    ->options([
                        'Tetap' => 'Tetap',
                        'Kontrak' => 'Kontrak',
                        'Magang' => 'Magang',
                    ]),
                Tables\Filters\SelectFilter::make('departemen')
                    ->label('Departemen')
                    ->query(fn (Builder $query, array $data) => $query->when($data['value'], fn ($q) => $q->where('departemen', $data['value']))),
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
            'index' => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}
