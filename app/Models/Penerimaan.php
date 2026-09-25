<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penerimaan extends Model
{
    use HasFactory;

    public const TERMIN_LUNAS = 'Lunas';
    public const TERMIN_BELUM_LUNAS = 'Belum Lunas';
    public const TERMIN_DP = 'DP';
    public const TERMIN_TERMIN_1 = 'Termin 1';
    public const TERMIN_TERMIN_2 = 'Termin 2';

    protected $fillable = [
        'tanggal',
        'nama_klien_proyek',
        'jenis_layanan',
        'nomor_po',
        'nilai_kontrak',
        'termin_status',
        'penerimaan_masuk',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nilai_kontrak' => 'decimal:2',
        'penerimaan_masuk' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->nilai_kontrak = is_numeric($model->nilai_kontrak)
                ? $model->nilai_kontrak
                : str_replace(['.', ','], ['', '.'], $model->nilai_kontrak ?? 0);
            $model->penerimaan_masuk = is_numeric($model->penerimaan_masuk)
                ? $model->penerimaan_masuk
                : str_replace(['.', ','], ['', '.'], $model->penerimaan_masuk ?? 0);
        });

        static::creating(function ($model) {
            $model->created_by = auth()->user()->id ?? null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user()->id ?? $model->created_by;
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'created_by', ownerKey: 'id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'updated_by', ownerKey: 'id');
    }

    public static function getNextNomorPO(): string
    {
        $prefix = 'PO-DBI';
        $year = now()->format('y');
        $month = now()->format('m');

        $lastSeq = static::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->whereNotNull('nomor_po')
            ->get('nomor_po')
            ->map(fn ($row) => (int) substr($row->nomor_po, -3))
            ->max() ?? 0;

        return $prefix . $year . $month . str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);
    }

    public static function getTerminStatusOptions(): array
    {
        return [
            self::TERMIN_LUNAS => 'Lunas',
            self::TERMIN_BELUM_LUNAS => 'Belum Lunas',
            self::TERMIN_TERMIN_1 => 'Termin 1 DP',
            self::TERMIN_TERMIN_2 => 'Termin 2',
        ];
    }
}
