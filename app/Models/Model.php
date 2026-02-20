<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
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
