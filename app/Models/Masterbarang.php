<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Masterbarang extends EloquentModel
{
    protected $fillable = [
        'tipe_id',
        'kategori_id',
        'merek_id',
        'model_id',
        'nama_barang',
        'isactive',
    ];

    public function tipe(): BelongsTo
    {
        return $this->belongsTo(Tipe::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function merek(): BelongsTo
    {
        return $this->belongsTo(Merek::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(Model::class);
    }
}
