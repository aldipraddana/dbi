<?php

namespace App\Enums;

enum KategoriGaji: string
{
    use \App\Traits\EnumTraits;

    case UANG_MAKAN = 'uang_makan';
    case LEMBUR = 'lembur';
    case PELUNASAN_PEKERJAAN = 'pelunasan_pekerjaan';
    case KAS_BON = 'kas_bon';
    case GAJI_BULANAN = 'gaji_bulanan';

    public function label(): string
    {
        return match ($this) {
            self::UANG_MAKAN => 'Uang Makan',
            self::LEMBUR => 'Lembur',
            self::PELUNASAN_PEKERJAAN => 'Pelunasan Pekerjaan',
            self::KAS_BON => 'Kas Bon',
            self::GAJI_BULANAN => 'Gaji Bulanan',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
