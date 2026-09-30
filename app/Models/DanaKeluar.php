<?php

namespace App\Models;

use App\Enums\DanaKeluarJenis;
use App\Enums\KategoriGaji;
use App\Enums\TipePekerja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DanaKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dana_keluars';

    protected $fillable = [
        'tanggal',
        'jenis',
        'kategori_gaji',
        'tipe_pekerja',
        'supplier_id',
        'kendaraan_id',
        'karyawan_id',
        'client_id',
        'nama_proyek',
        'nama_mandor',
        'nama_pemborong',
        'nama_pekerjaan',
        'keperluan',
        'item',
        'qty',
        'nominal',
        'potongan_kas_bon',
        'total',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jenis' => DanaKeluarJenis::class,
            'kategori_gaji' => KategoriGaji::class,
            'tipe_pekerja' => TipePekerja::class,
            'qty' => 'decimal:4',
            'nominal' => 'decimal:2',
            'potongan_kas_bon' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(DanaKeluarItem::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Scope: sisa kas bon belum lunas untuk karyawan
    public static function getSisaKasBonKaryawan(int $karyawanId): float
    {
        return self::where('jenis', DanaKeluarJenis::GAJI)
            ->where('kategori_gaji', KategoriGaji::KAS_BON)
            ->where('karyawan_id', $karyawanId)
            ->where('tipe_pekerja', TipePekerja::HARIAN)
            ->sum('total');
    }

    // Scope: sisa kas bon belum lunas untuk pemborong (by nama_pemborong)
    public static function getSisaKasBonPemborong(string $namaPemborong): float
    {
        return self::where('jenis', DanaKeluarJenis::GAJI)
            ->where('kategori_gaji', KategoriGaji::KAS_BON)
            ->where('tipe_pekerja', TipePekerja::BORONGAN)
            ->whereRaw('LOWER(nama_pemborong) = ?', [strtolower($namaPemborong)])
            ->sum('total');
    }

    public function getSummaryLabelAttribute(): string
    {
        return match ($this->jenis) {
            DanaKeluarJenis::OPERASIONAL => $this->karyawan?->nama_lengkap ?? $this->kendaraan?->nopol ?? '-',
            DanaKeluarJenis::BAHAN_BAKU => $this->supplier?->nama_supplier ?? '-',
            DanaKeluarJenis::GAJI => $this->karyawan?->nama_lengkap
                ?? $this->nama_pemborong
                ?? $this->nama_mandor
                ?? KategoriGaji::tryFrom($this->kategori_gaji?->value ?? '')?->label()
                ?? '-',
            DanaKeluarJenis::BIAYA_KANTOR,
            DanaKeluarJenis::BIAYA_LAIN => $this->item ?? '-',
        };
    }
}
