<?php

declare(strict_types=1);

namespace App\Services\LaporanKeuangan;

use App\Enums\JenisLaporanEnum;
use App\Exports\LaporanKeuangan\BaseLaporanSheet;
use App\Exports\LaporanKeuangan\CashFlowSheet;
use App\Exports\LaporanKeuangan\CatatanKeuanganSheet;
use App\Exports\LaporanKeuangan\NeracaSheet;
use App\Exports\LaporanKeuangan\PerubahanEkuitasSheet;
use App\Exports\LaporanKeuangan\ProfitLossSheet;
use Carbon\Carbon;
use InvalidArgumentException;

/**
 * Pabrik sheet laporan: satu-satunya tempat pemetaan jenis laporan -> kelas sheet,
 * dipakai export Excel maupun halaman print supaya keduanya menyusun laporan yang sama.
 */
class LaporanSheetFactory
{
    /**
     * Jenis laporan yang menghasilkan sheet. `Semua` = kelima laporan berurutan.
     *
     * @return array<int, JenisLaporanEnum>
     */
    public static function jenisDipilih(JenisLaporanEnum $jenis): array
    {
        return $jenis === JenisLaporanEnum::Semua
            ? JenisLaporanEnum::laporan()
            : [$jenis];
    }

    public static function buat(
        JenisLaporanEnum $jenis,
        LaporanSheetWriter $writer,
        Carbon $mulai,
        Carbon $akhir,
        LaporanKeuanganDataService $data,
    ): BaseLaporanSheet {
        return match ($jenis) {
            JenisLaporanEnum::Neraca => new NeracaSheet($writer, $mulai, $akhir, $data),
            JenisLaporanEnum::ProfitLoss => new ProfitLossSheet($writer, $mulai, $akhir, $data),
            JenisLaporanEnum::CashFlow => new CashFlowSheet($writer, $mulai, $akhir, $data),
            JenisLaporanEnum::PerubahanEkuitas => new PerubahanEkuitasSheet($writer, $mulai, $akhir, $data),
            JenisLaporanEnum::CatatanKeuangan => new CatatanKeuanganSheet($writer, $mulai, $akhir, $data),
            default => throw new InvalidArgumentException("Jenis laporan [{$jenis->value}] tidak memiliki sheet."),
        };
    }
}
