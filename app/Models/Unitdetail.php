<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unitdetail extends Model
{
    protected $fillable = [
        'unit_id',
        'no_polisi',
        'no_mesin',
        'no_rangka',
        'tahun',
        'warna',
        'km',
        'nama_bpkb',
        'alamat_bpkb',
        'no_bpkb',
        'masa_berlaku_pajak',
        'masa_berlaku_stnk',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    protected function casts(): array
    {
        return [
            'masa_berlaku_pajak' => 'date',
            'masa_berlaku_stnk' => 'date',
        ];
    }
}
