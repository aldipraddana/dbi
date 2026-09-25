<?php

namespace App\Enums;

enum JenisLaporanEnum: string
{
    case Neraca = 'neraca';
    case ProfitLoss = 'profit_loss';
    case CashFlow = 'cash_flow';
    case PerubahanEkuitas = 'perubahan_ekuitas';
    case CatatanKeuangan = 'catatan_keuangan';

    public function label(): string
    {
        return match ($this) {
            self::Neraca => 'Laporan Posisi Keuangan (Neraca)',
            self::ProfitLoss => 'Profit and Loss',
            self::CashFlow => 'Cash Flow',
            self::PerubahanEkuitas => 'Laporan Perubahan Ekuitas',
            self::CatatanKeuangan => 'Catatan atas Laporan Keuangan',
        };
    }

    public function fileName(): string
    {
        return match ($this) {
            self::Neraca => 'Laporan_Posisi_Keuangan_Neraca',
            self::ProfitLoss => 'Profit_and_Loss',
            self::CashFlow => 'Cash_Flow',
            self::PerubahanEkuitas => 'Laporan_Perubahan_Ekuitas',
            self::CatatanKeuangan => 'Catatan_atas_Laporan_Keuangan',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}
