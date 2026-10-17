<?php

if (! function_exists('generateSequentialNumberAsset')) {
    function generateSequentialNumberAsset(string $model, ?string $code = null, string $column = 'asset_number'): string
    {
        $initials = $code.date('ym');
        $lastRecord = $model::where($column, 'like', "$initials%")->latest('id')->first();

        $lastNumber = $lastRecord ? intval(substr($lastRecord->$column, strlen($initials))) : 0;
        $newNumber = $lastNumber + 1;
        return $initials . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('format_nilai_laporan')) {
    /**
     * Angka rupiah pada laporan keuangan, mengikuti format sel Excel
     * `#,##0;(#,##0);-`: positif `1.234`, nol `-`, negatif `(1.234)`.
     *
     * Dipakai halaman print supaya hasilnya sama dengan tampilan Excel.
     */
    function format_nilai_laporan(int|float|null $nilai): string
    {
        if ($nilai === null) {
            return '';
        }

        if ($nilai == 0) {
            return '-';
        }

        $terformat = number_format(abs((float) $nilai), 0, ',', '.');

        return $nilai < 0 ? "({$terformat})" : $terformat;
    }
}

if (! function_exists('format_persen_laporan')) {
    /**
     * Persentase laporan, mengikuti format sel Excel `0.0%` (input rasio 0-1).
     * Pemisah desimal memakai koma, mengikuti gaya Indonesia pada format rupiah.
     */
    function format_persen_laporan(int|float|null $rasio): string
    {
        if ($rasio === null) {
            return '';
        }

        return number_format((float) $rasio * 100, 1, ',', '.') . '%';
    }
}
