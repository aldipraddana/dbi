<?php

namespace App\Filament\Resources\LaporanKeuanganHistoryResource;

use App\Constants\UserMenuConstant;
use App\Filament\Resources\LaporanKeuanganHistoryResource\Pages;
use App\Models\LaporanKeuanganHistory;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LaporanKeuanganHistoryResource extends Resource
{
    protected static ?string $label = 'History Laporan Keuangan';
    protected static ?string $model = LaporanKeuanganHistory::class;
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'History Laporan Keuangan';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->disabled(),
            Forms\Components\TextInput::make('jenis_laporan')
                ->label('Jenis Laporan')
                ->disabled(),
            Forms\Components\DatePicker::make('tanggal_mulai')
                ->label('Tanggal Mulai')
                ->native(false)
                ->displayFormat('d F Y')
                ->disabled(),
            Forms\Components\DatePicker::make('tanggal_akhir')
                ->label('Tanggal Akhir')
                ->native(false)
                ->displayFormat('d F Y')
                ->disabled(),
            Forms\Components\TextInput::make('format')
                ->label('Format')
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                $query->when(! $user->isAdmin(), function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                });
                $query->orderByDesc('created_at');
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Export')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_laporan_label')
                    ->label('Jenis Laporan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->sortable(),
                Tables\Columns\TextColumn::make('format')
                    ->label('Format')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'excel' => 'success',
                        'pdf' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_laporan')
                    ->label('Jenis Laporan')
                    ->options(\App\Enums\JenisLaporanEnum::options()),
                Tables\Filters\SelectFilter::make('format')
                    ->label('Format')
                    ->options([
                        'excel' => 'Excel',
                        'pdf' => 'PDF',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKeuanganHistory::route('/'),
            'view' => Pages\ViewLaporanKeuanganHistory::route('/{record}'),
        ];
    }
}
