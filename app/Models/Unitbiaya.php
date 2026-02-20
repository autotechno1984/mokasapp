<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unitbiaya extends Model
{
    protected $fillable = [
        'unit_id',
        'kategori',
        'keterangan',
        'amount',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
