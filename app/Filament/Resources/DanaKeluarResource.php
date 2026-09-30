<?php

namespace App\Filament\Resources\DanaKeluarResource;

use App\Constants\UserMenuConstant;
use App\Enums\DanaKeluarJenis;
use App\Enums\KategoriGaji;
use App\Enums\TipePekerja;
use App\Filament\Resources\DanaKeluarResource\Pages;
use App\Models\DanaKeluar;
use App\Models\DanaKeluarItem;
use App\Models\Karyawan;
use App\Models\PengadaanStockDetail;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource as BaseResource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DanaKeluarResource extends BaseResource
{
    protected static ?string $model = DanaKeluar::class;
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = UserMenuConstant::MENU_DANA_KELUAR;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-left';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([self::level1Fields()]);
    }

    // ================================================================
    // LEVEL 1 — Selalu tampil
    // ================================================================
    private static function level1Fields(): array
    {
        return [
            Section::make('Data Dana Keluar')
                ->schema([
                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->required()
                        ->native(false)
                        ->displayFormat('d F Y')
                        ->default(now()),
                    Select::make('jenis')
                        ->label('Jenis Pengeluaran')
                        ->required()
                        ->options(DanaKeluarJenis::options())
                        ->live()
                        ->afterStateUpdated(function (Get $get, callable $set) {
                            // Reset semua field level 2 saat jenis berubah
                            $set('kategori_gaji', null);
                            $set('tipe_pekerja', null);
                            $set('supplier_id', null);
                            $set('kendaraan_id', null);
                            $set('karyawan_id', null);
                            $set('client_id', null);
                            $set('nama_proyek', null);
                            $set('nama_mandor', null);
                            $set('nama_pemborong', null);
                            $set('nama_pekerjaan', null);
                            $set('keperluan', null);
                            $set('item', null);
                            $set('qty', null);
                            $set('nominal', null);
                            $set('potongan_kas_bon', null);
                            $set('keterangan', null);
                            $set('items', []);
                            $set('sisa_kas_bon', null);
                        }),
                ])
                ->columns(2),

            // ================================================================
            // OPERASIONAL
            // ================================================================
            Section::make('Detail Operasional')
                ->schema([
                    Select::make('kendaraan_id')
                        ->label('Kendaraan')
                        ->relationship('kendaraan', 'nopol')
                        ->searchable()->preload(),
                    Select::make('karyawan_id')
                        ->label('Karyawan')
                        ->relationship('karyawan', 'nama_lengkap')
                        ->searchable()->preload(),
                    TextInput::make('keperluan')->label('Keperluan')->maxLength(255),
                    self::nominalField('nominal', DanaKeluarJenis::OPERASIONAL->value),
                ])
                ->columns(2)
                ->visible(fn(Get $get) => $get('jenis')?->value === DanaKeluarJenis::OPERASIONAL->value),

            // ================================================================
            // BAHAN BAKU
            // ================================================================
            Section::make('Detail Bahan Baku')
                ->schema([
                    // Supplier level-1 harus dipilih dulu agar repeater aktif
                    Select::make('supplier_id')
                        ->label('Supplier')
                        ->relationship('supplier', 'nama_supplier')
                        ->searchable()->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn(Get $get, callable $set) => $set('items', [])),
                    // Repeater items: filter barang berdasarkan supplier
                    self::bahanBakuRepeater(),
                    // Total read-only
                    Placeholder::make('total_bahan_baku')
                        ->label('Total')
                        ->content(function (Get $get) {
                            $items = $get('items') ?? [];
                            $total = collect($items)->sum(fn($item) => (float) ($item['subtotal'] ?? 0));
                            return 'Rp ' . number_format($total, 0, ',', '.');
                        }),
                ])
                ->visible(fn(Get $get) => $get('jenis')?->value === DanaKeluarJenis::BAHAN_BAKU->value),

            // ================================================================
            // GAJI
            // ================================================================
            Section::make('Detail Gaji')
                ->schema([
                    Select::make('kategori_gaji')
                        ->label('Kategori Gaji')
                        ->required()
                        ->options(KategoriGaji::options())
                        ->live()
                        ->afterStateUpdated(fn(Get $get, callable $set) => tap($set)([
                            'karyawan_id' => null,
                            'client_id' => null,
                            'nama_proyek' => null,
                            'nama_mandor' => null,
                            'nama_pemborong' => null,
                            'nama_pekerjaan' => null,
                            'nominal' => null,
                            'potongan_kas_bon' => null,
                            'keterangan' => null,
                            'tipe_pekerja' => null,
                            'sisa_kas_bon' => null,
                        ])),

                    // --- Uang Makan & Lembur ---
                    Select::make('karyawan_id')
                        ->label('Karyawan')
                        ->relationship('karyawan', 'nama_lengkap')
                        ->searchable()->preload()
                        ->visible(fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::UANG_MAKAN->value, KategoriGaji::LEMBUR->value])),
                    TextInput::make('nama_proyek')
                        ->label('Nama Proyek')
                        ->visible(fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::UANG_MAKAN->value, KategoriGaji::LEMBUR->value])),
                    self::nominalField('nominal', fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::UANG_MAKAN->value, KategoriGaji::LEMBUR->value])),

                    // --- Pelunasan Pekerjaan ---
                    Select::make('client_id')
                        ->label('Client')
                        ->relationship('client', 'nama_client')
                        ->searchable()->preload()
                        ->visible(fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::PELUNASAN_PEKERJAAN->value])),
                    TextInput::make('nama_mandor')->label('Nama Mandor')
                        ->visible(fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::PELUNASAN_PEKERJAAN->value])),
                    TextInput::make('nama_pekerjaan')->label('Nama Pekerjaan')
                        ->visible(fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::PELUNASAN_PEKERJAAN->value])),
                    self::nominalField('nominal', fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::PELUNASAN_PEKERJAAN->value])),
                    Textarea::make('keterangan')->label('Keterangan')->rows(3)
                        ->visible(fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::PELUNASAN_PEKERJAAN->value])),

                    // --- Kas Bon ---
                    Select::make('tipe_pekerja')
                        ->label('Tipe Pekerja')
                        ->options(TipePekerja::options())
                        ->live()
                        ->afterStateUpdated(fn(Get $get, callable $set) => tap($set)([
                            'karyawan_id' => null,
                            'nama_pemborong' => null,
                        ]))
                        ->visible(fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::KAS_BON->value])),
                    Select::make('karyawan_id')
                        ->label('Karyawan (Harian)')
                        ->relationship('karyawan', 'nama_lengkap')
                        ->searchable()->preload()
                        ->options(fn() => Karyawan::where('jenis', 'harian')->pluck('nama_lengkap', 'id'))
                        ->visible(fn(Get $get) => self::isGajiTipe($get, KategoriGaji::KAS_BON->value, TipePekerja::HARIAN->value)),
                    TextInput::make('nama_pemborong')->label('Nama Pemborong')
                        ->visible(fn(Get $get) => self::isGajiTipe($get, KategoriGaji::KAS_BON->value, TipePekerja::BORONGAN->value)),
                    self::nominalField('nominal', fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::KAS_BON->value])),

                    // --- Gaji Bulanan ---
                    Select::make('tipe_pekerja')
                        ->label('Tipe Pekerja')
                        ->options(TipePekerja::options())
                        ->live()
                        ->afterStateUpdated(fn(Get $get, callable $set) => tap($set)([
                            'karyawan_id' => null,
                            'nama_pemborong' => null,
                            'nama_proyek' => null,
                            'potongan_kas_bon' => null,
                            'sisa_kas_bon' => null,
                        ]))
                        ->visible(fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::GAJI_BULANAN->value])),

                    // Gaji Bulanan - Harian
                    Select::make('karyawan_id')
                        ->label('Karyawan (Harian)')
                        ->relationship('karyawan', 'nama_lengkap')
                        ->searchable()->preload()
                        ->options(fn() => Karyawan::where('jenis', 'harian')->pluck('nama_lengkap', 'id'))
                        ->live()
                        ->afterStateUpdated(function ($state, Get $get, callable $set) {
                            if ($state) {
                                $sisa = DanaKeluar::getSisaKasBonKaryawan((int) $state);
                                $set('sisa_kas_bon', $sisa);
                                $set('potongan_kas_bon', min($sisa, (float) ($get('nominal') ?? 0)));
                            }
                        })
                        ->visible(fn(Get $get) => self::isGajiTipe($get, KategoriGaji::GAJI_BULANAN->value, TipePekerja::HARIAN->value)),
                    Placeholder::make('sisa_kas_bon_harian')
                        ->label('Total Kas Bon Belum Lunas')
                        ->content(fn(Get $get) => 'Rp ' . number_format((float) ($get('sisa_kas_bon') ?? 0), 0, ',', '.')),

                    // Gaji Bulanan - Borongan
                    TextInput::make('nama_proyek')->label('Nama Proyek')
                        ->visible(fn(Get $get) => self::isGajiTipe($get, KategoriGaji::GAJI_BULANAN->value, TipePekerja::BORONGAN->value)),
                    TextInput::make('nama_pemborong')
                        ->label('Nama Pemborong')
                        ->live()
                        ->afterStateUpdated(function ($state, Get $get, callable $set) {
                            if ($state) {
                                $sisa = DanaKeluar::getSisaKasBonPemborong($state);
                                $set('sisa_kas_bon', $sisa);
                                $set('potongan_kas_bon', min($sisa, (float) ($get('nominal') ?? 0)));
                            }
                        })
                        ->visible(fn(Get $get) => self::isGajiTipe($get, KategoriGaji::GAJI_BULANAN->value, TipePekerja::BORONGAN->value)),
                    Placeholder::make('sisa_kas_bon_borongan')
                        ->label('Total Kas Bon Belum Lunas')
                        ->content(fn(Get $get) => 'Rp ' . number_format((float) ($get('sisa_kas_bon') ?? 0), 0, ',', '.')),

                    // Nominal, Potongan, Total Gaji Bulanan
                    self::nominalField('nominal', fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::GAJI_BULANAN->value]))
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Get $get, callable $set) {
                            $nominal = (float) preg_replace('/\D/', '', (string) ($state ?? 0));
                            $sisa = (float) ($get('sisa_kas_bon') ?? 0);
                            $set('potongan_kas_bon', min($sisa, $nominal));
                        }),
                    self::nominalField('potongan_kas_bon', fn(Get $get) => self::isGajiKategori($get, [KategoriGaji::GAJI_BULANAN->value]))
                        ->live(onBlur: true),
                    Placeholder::make('total_gaji_bulanan')
                        ->label('Total (Nominal - Potongan Kas Bon)')
                        ->content(fn(Get $get) => 'Rp ' . number_format(max(0, (float) ($get('nominal') ?? 0) - (float) ($get('potongan_kas_bon') ?? 0)), 0, ',', '.')),
                ])
                ->visible(fn(Get $get) => $get('jenis')?->value === DanaKeluarJenis::GAJI->value),

            // ================================================================
            // BIAYA KANTOR & BIAYA LAIN-LAIN
            // ================================================================
            Section::make('Detail Biaya Kantor / Biaya Lain')
                ->schema([
                    TextInput::make('item')->label('Nama Item / Biaya')->maxLength(255),
                    TextInput::make('qty')
                        ->label('Qty')
                        ->numeric()->minValue(0)
                        ->live()
                        ->afterStateUpdated(fn(Get $get, callable $set) => $set('total',
                            (float) ($get('qty') ?? 0) * (float) ($get('nominal') ?? 0)
                        )),
                    self::nominalField('nominal', true)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(Get $get, callable $set) => $set('total',
                            (float) ($get('qty') ?? 0) * (float) ($get('nominal') ?? 0)
                        )),
                    Placeholder::make('total_biaya')
                        ->label('Total (Qty × Nominal)')
                        ->content(fn(Get $get) => 'Rp ' . number_format(
                            (float) ($get('qty') ?? 0) * (float) ($get('nominal') ?? 0), 0, ',', '.'
                        )),
                    Textarea::make('keterangan')->label('Keterangan')->rows(3),
                ])
                ->columns(2)
                ->visible(fn(Get $get) => in_array($get('jenis')?->value, [
                    DanaKeluarJenis::BIAYA_KANTOR->value,
                    DanaKeluarJenis::BIAYA_LAIN->value,
                ], true)),
        ];
    }

    // ================================================================
    // HELPER — cek apakah jenis GAJI dan kategori_gaji cocok
    // ================================================================
    private static function isGajiKategori(Get $get, array $kategoriValues): bool
    {
        return $get('jenis')?->value === DanaKeluarJenis::GAJI->value
            && in_array($get('kategori_gaji')?->value, $kategoriValues, true);
    }

    // Helper: cek apakah jenis GAJI, kategori_gaji DAN tipe_pekerja cocok
    private static function isGajiTipe(Get $get, string $kategori, string $tipe): bool
    {
        return $get('jenis')?->value === DanaKeluarJenis::GAJI->value
            && $get('kategori_gaji')?->value === $kategori
            && $get('tipe_pekerja')?->value === $tipe;
    }

    // ================================================================
    // FIELD HELPER — Nominal dengan format Rupiah
    // ================================================================
    private static function nominalField(string $name, callable|bool $condition): TextInput
    {
        return TextInput::make($name)
            ->label(ucfirst(str_replace('_', ' ', $name)))
            ->numeric()
            ->prefix('Rp')
            ->dehydrateStateUsing(fn($state): float => (float) preg_replace('/\D/', '', (string) ($state ?? 0)))
            ->formatStateUsing(fn($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
            ->visible($condition);
    }

    // ================================================================
    // REPEATER — Items untuk Bahan Baku
    // Filter barang berdasarkan supplier_id di level 1
    // ================================================================
    private static function bahanBakuRepeater(): Repeater
    {
        return Repeater::make('items')
            ->label('Items Bahan Baku')
            ->relationship('items')
            ->schema([
                // Select barang: opsi HANYA dari pengadaan stock supplier terpilih
                Select::make('pengadaan_stock_id')
                    ->label('Barang (Pengadaan Stock)')
                    ->options(function (Get $get) {
                        $supplierId = $get('../../supplier_id');
                        if (! $supplierId) {
                            return [];
                        }
                        return PengadaanStockDetail::whereHas('pengadaanStock', fn($q) => $q->where('supplier_id', $supplierId))
                            ->with('pengadaanStock.supplier')
                            ->get()
                            ->mapWithKeys(fn($detail) => [
                                $detail->id => sprintf(
                                    '%s | Supplier: %s | Qty: %s',
                                    $detail->nama_barang,
                                    $detail->pengadaanStock->supplier->nama_supplier ?? '-',
                                    $detail->qty
                                ),
                            ]);
                    })
                    ->searchable()
                    ->preload(false)
                    ->live()
                    ->disabled(fn(Get $get) => ! $get('../../supplier_id'))
                    // Saat barang dipilih, auto-fill field read-only
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if (! $state) {
                            $set('nama_barang', null);
                            $set('qty', null);
                            $set('harga_satuan', null);
                            $set('subtotal', null);
                            return;
                        }
                        $detail = PengadaanStockDetail::with('pengadaanStock.supplier')->find($state);
                        if ($detail) {
                            $harga = (float) ($detail->harga_satuan ?? 0);
                            $qty = (float) ($detail->qty ?? 0);
                            $set('nama_barang', $detail->nama_barang);
                            $set('qty', $detail->qty);
                            $set('harga_satuan', number_format($harga, 0, ',', '.'));
                            $set('subtotal', number_format($harga * $qty, 0, ',', '.'));
                        }
                    }),

                TextInput::make('nama_barang')
                    ->label('Nama Barang')
                    ->disabled()
                    ->dehydrated(false),

                Placeholder::make('qty_info')
                    ->label('Qty Tersedia')
                    ->content(function (Get $get) {
                        $detailId = $get('pengadaan_stock_id');
                        if (! $detailId) {
                            return '-';
                        }
                        $detail = PengadaanStockDetail::find($detailId);
                        return $detail ? (string) $detail->qty : '-';
                    }),

                TextInput::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->prefix('Rp')
                    ->live(onBlur: true)
                    ->dehydrateStateUsing(fn($state): float => (float) preg_replace('/\D/', '', (string) ($state ?? 0)))
                    ->afterStateUpdated(function (Get $get, callable $set) {
                        $harga = (float) preg_replace('/\D/', '', (string) ($get('harga_satuan') ?? '0'));
                        $qty = (float) ($get('qty') ?? 0);
                        $set('harga_satuan', number_format($harga, 0, ',', '.'));
                        $set('subtotal', number_format($harga * $qty, 0, ',', '.'));
                    }),

                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn($state) => $state ? 'Rp ' . number_format((float) $state, 0, ',', '.') : 'Rp 0'),
            ])
            ->columns(5)
            ->addActionLabel('Tambah Item')
            ->defaultItems(1)
            ->reorderable(false)
            ->columnSpanFull();
    }

    // ================================================================
    // TABLE / LIST
    // ================================================================
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->formatStateUsing(fn($state) => $state?->label() ?? $state)
                    ->badge()
                    ->color(fn($state) => $state?->color() ?? 'gray'),

                TextColumn::make('kategori_gaji')
                    ->label('Kategori Gaji')
                    ->formatStateUsing(fn($state) => $state?->label() ?? '-')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('summary_label')
                    ->label('Ringkasan')
                    ->getStateUsing(fn(DanaKeluar $record) => $record->summary_label)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('total')
                    ->label('Total')
                    ->sortable()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->summarize(Sum::make()->label('Total: Rp ')
                        ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Jenis')
                    ->options(DanaKeluarJenis::options()),
                Tables\Filters\SelectFilter::make('kategori_gaji')
                    ->label('Kategori Gaji')
                    ->options(KategoriGaji::options()),
                Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query
                        ->when($data['dari_tanggal'] ?? null, fn($q) => $q->whereDate('tanggal', '>=', $data['dari_tanggal']))
                        ->when($data['sampai_tanggal'] ?? null, fn($q) => $q->whereDate('tanggal', '<=', $data['sampai_tanggal']))),
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
            ->defaultSort('tanggal', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDanaKeluars::route('/'),
            'create' => Pages\CreateDanaKeluar::route('/create'),
            'view' => Pages\ViewDanaKeluar::route('/{record}'),
            'edit' => Pages\EditDanaKeluar::route('/{record}/edit'),
        ];
    }
}
