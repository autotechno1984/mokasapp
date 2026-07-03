<?php

namespace App\Models;

use App\Support\Terbilang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kwitansi extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'nomor',
        'tanggal',
        'nama_penerima',
        'untuk_pembayaran',
        'jumlah',
        'metode',
        'unit_id',
        'status',
        'catatan',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function isBatal(): bool
    {
        return $this->status === 'batal';
    }

    /**
     * Jumlah dalam bentuk terbilang (Bahasa Indonesia), mis. "satu juta rupiah".
     */
    public function terbilang(): string
    {
        return Terbilang::rupiah((float) $this->jumlah);
    }

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'decimal:2',
        ];
    }
}
