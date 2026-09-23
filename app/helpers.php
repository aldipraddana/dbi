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