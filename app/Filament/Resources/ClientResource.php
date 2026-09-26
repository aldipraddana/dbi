<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    public static ?string $label = UserMenuConstant::MENU_CLIENT;
    protected static ?string $model = Client::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_CLIENT;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Client')
                    ->schema([
                        Forms\Components\TextInput::make('kode_client')
                            ->label('Kode Client')
                            ->disabled()
                            ->dehydrated()
                            ->extraAttributes(['class' => 'bg-gray-100 font-bold']),
                        Forms\Components\TextInput::make('nama_client')
                            ->label('Nama Client')
                            ->required()
                            ->placeholder('Masukkan nama client')
                            ->extraInputAttributes([
                                'onInput' => "this.value = this.value.replace(/\b\w/g, c => c.toUpperCase())"
                            ])
                            ->maxLength(255),
                        Forms\Components\Select::make('jenis_client')
                            ->label('Jenis Client')
                            ->placeholder('Pilih jenis client')
                            ->options([
                                'Perusahaan' => 'Perusahaan',
                                'Perorangan' => 'Perorangan',
                            ]),
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
                        Forms\Components\TextInput::make('npwp')
                            ->label('NPWP')
                            ->placeholder('Masukkan NPWP')
                            ->maxLength(50),
                        Forms\Components\Toggle::make('status')
                            ->label('Status Aktif')
                            ->inline(false)
                            ->default(true)
                            ->required(),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->placeholder('Masukkan catatan')
                            ->rows(2)
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
                            ->visible(fn (Client $record) => $record->trashed())
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('deleted_at')
                            ->label('Dihapus Pada')
                            ->displayFormat('d F Y H:i:s')
                            ->native(false)
                            ->visible(fn (Client $record) => $record->trashed())
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
                Tables\Columns\TextColumn::make('kode_client')
                    ->label('Kode')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nama_client')
                    ->label('Nama Client')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_client')
                    ->label('Jenis')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('no_telepon')
                    ->label('No. Telepon')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kota')
                    ->label('Kota')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ]),
                Tables\Filters\SelectFilter::make('jenis_client')
                    ->label('Jenis Client')
                    ->options([
                        'Perusahaan' => 'Perusahaan',
                        'Perorangan' => 'Perorangan',
                    ]),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
