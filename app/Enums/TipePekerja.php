<?php

namespace App\Enums;

enum TipePekerja: string
{
    use \App\Traits\EnumTraits;

    case HARIAN = 'harian';
    case BORONGAN = 'borongan';

    public function label(): string
    {
        return match ($this) {
            self::HARIAN => 'Harian',
            self::BORONGAN => 'Borongan',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
