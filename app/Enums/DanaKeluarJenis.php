<?php

namespace App\Enums;

enum DanaKeluarJenis: string
{
    use \App\Traits\EnumTraits;

    case OPERASIONAL = 'operasional';
    case BAHAN_BAKU = 'bahan_baku';
    case GAJI = 'gaji';
    case BIAYA_KANTOR = 'biaya_kantor';
    case BIAYA_LAIN = 'biaya_lain';

    public function label(): string
    {
        return match ($this) {
            self::OPERASIONAL => 'Operasional',
            self::BAHAN_BAKU => 'Bahan Baku',
            self::GAJI => 'Gaji',
            self::BIAYA_KANTOR => 'Biaya Kantor',
            self::BIAYA_LAIN => 'Biaya Lain-Lain',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPERASIONAL => 'info',
            self::BAHAN_BAKU => 'warning',
            self::GAJI => 'success',
            self::BIAYA_KANTOR => 'gray',
            self::BIAYA_LAIN => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
