<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Biaya extends Model
{
    protected $fillable = [
        'tenant_id',
        'kategori',
        'tanggal',
        'keterangan',
        'jumlah',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }
}
