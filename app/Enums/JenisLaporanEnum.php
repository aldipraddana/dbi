<?php

declare(strict_types=1);

namespace App\Enums;

enum JenisLaporanEnum: string
{
    case Neraca = 'neraca';
    case ProfitLoss = 'profit_loss';
    case CashFlow = 'cash_flow';
    case PerubahanEkuitas = 'perubahan_ekuitas';
    case CatatanKeuangan = 'catatan_keuangan';
    case Semua = 'semua';

    public function label(): string
    {
        return match ($this) {
            self::Neraca => 'Laporan Posisi Keuangan (Neraca)',
            self::ProfitLoss => 'Profit and Loss',
            self::CashFlow => 'Cash Flow',
            self::PerubahanEkuitas => 'Laporan Perubahan Ekuitas',
            self::CatatanKeuangan => 'Catatan atas Laporan Keuangan',
            self::Semua => 'Semua Jenis Laporan',
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
            self::Semua => 'Semua_Jenis_Laporan',
        };
    }

    /**
     * Nama sheet Excel (maksimal 31 karakter, tanpa karakter : \ / ? * [ ]).
     */
    public function sheetName(): string
    {
        return match ($this) {
            self::Neraca => 'Neraca',
            self::ProfitLoss => 'Profit and Loss',
            self::CashFlow => 'Cash Flow',
            self::PerubahanEkuitas => 'Perubahan Ekuitas',
            self::CatatanKeuangan => 'Catatan Keuangan',
            self::Semua => 'Semua Jenis Laporan',
        };
    }

    /**
     * Daftar jenis laporan yang menghasilkan sheet (tanpa `Semua`),
     * urutan ini juga dipakai saat export "Semua Jenis Laporan".
     *
     * @return array<int, self>
     */
    public static function laporan(): array
    {
        return [
            self::Neraca,
            self::ProfitLoss,
            self::CashFlow,
            self::PerubahanEkuitas,
            self::CatatanKeuangan,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}
