<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class MonitoringStock extends Model
{
    protected $table = 'v_monitoring_stock';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'stok_saat_ini' => 'integer',
            'client_id' => 'integer',
            'supplier_id' => 'integer',
        ];
    }

    protected function performInsert(Builder $query): bool
    {
        throw new LogicException('MonitoringStock hanya untuk dibaca (read-only).');
    }

    protected function performUpdate(Builder $query): bool
    {
        throw new LogicException('MonitoringStock hanya untuk dibaca (read-only).');
    }

    public function delete(): bool
    {
        throw new LogicException('MonitoringStock hanya untuk dibaca (read-only).');
    }
}
