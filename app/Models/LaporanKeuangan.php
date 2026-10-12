<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Subjek Resource untuk halaman Laporan Keuangan.
 *
 * Halaman ini bukan CRUD, sehingga model ini sengaja tidak memiliki tabel
 * sendiri dan tidak pernah dipakai untuk query data (hanya dipakai Filament
 * untuk penamaan, URL, dan pengecekan policy).
 */
class LaporanKeuangan extends Model
{
    protected $table = 'laporan_keuangans';

    public $timestamps = false;

    protected $guarded = [];
}
