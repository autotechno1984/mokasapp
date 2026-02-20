<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penjualan extends Model
{
    protected $fillable = [
        'tenant_id',
        'unit_id',
        'tgl_jual',
        'nama_konsumen',
        'alamat',
        'kontak',
        'harga_jual',
        'status_pembelian',
        'leasing',
        'catatan',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    protected function casts(): array
    {
        return [
            'tgl_jual' => 'datetime',
        ];
    }
}
