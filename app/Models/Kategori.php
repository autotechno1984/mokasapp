<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = [
        'nama',
        'isactive',
    ];

    protected function casts(): array
    {
        return [
            'isactive' => 'boolean',
        ];
    }
}
