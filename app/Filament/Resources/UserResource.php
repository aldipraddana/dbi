<?php

namespace App\Filament\Resources;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\UserMenu;
use App\Models\UserPosition;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    public static ?string $label = UserMenuConstant::MENU_USER_MANAGEMENT;
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_USER_MANAGEMENT;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data User')
                    ->key('userData')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Nama')
                            ->placeholder('Masukkan nama lengkap')
                            ->extraInputAttributes([
                                'onInput' => "this.value = this.value.replace(/\b\w/g, c => c.toUpperCase())"
                            ])
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('Nomor Telepon / HP')
                            ->placeholder('Masukkan nomor telepon atau HP')
                            ->tel()
                            ->unique(ignorable: fn (?Model $record) => $record)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('username')
                            ->label('Username')
                            ->placeholder('Masukkan username')
                            ->minLength(3)
                            ->required()
                            ->readOnly(function($state) {
                                return $state == 'admin';
                            })
                            ->unique(ignorable: fn (?Model $record) => $record)
                            ->maxLength(255),

                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                'member' => 'Member',
                                'admin' => 'Admin',
                            ])
                            ->default('member')
                            ->required()
                            ->visible(fn () => Auth::user()->isAdmin()),

                        Forms\Components\TextInput::make('password')
                            ->minLength(5)
                            ->password()
                            ->placeholder('Masukkan password')
                            ->confirmed()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->placeholder('Ulangi password')
                            ->same('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),

                        Forms\Components\Select::make('menus')
                            ->label('Hak Akses Menu')
                            ->placeholder('Pilih menu yang dapat diakses')
                            ->multiple()
                            ->relationship('menus', 'name')
                            ->searchable()
                            ->visible(fn () => Auth::user()->isAdmin())
                            ->preload()
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Informasi Tambahan')
                    ->key('additionalInformation')
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
                        Forms\Components\Select::make('deleted_by')
                            ->relationship('deleter', 'name')
                            ->label('Dihapus Oleh')
                            ->visible(fn (User $record) => $record->trashed())
                            ->disabled(),
                        Forms\Components\DatePicker::make('deleted_at')
                            ->label('Dihapus Pada Tanggal')
                            ->displayFormat('d F Y H:i:s')
                            ->native(false)
                            ->visible(fn (User $record) => $record->trashed())
                            ->disabled(true),
                    ])
                    ->columns(2)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Nomor Telepon / HP')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updater.name')
                    ->label('Diperbarui Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        if (auth()->user()?->isAdmin() !== true) {
            $query->whereKey(auth()->id());
        }

        return $query;
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
