<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'Kode',
        'nama',
        'harga_bulanan',
        'harga_tahunan',
        'max_user',
        'max_cabang',
        'fitur',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fitur' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
