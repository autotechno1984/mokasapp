<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{

    protected $fillable = [
        'tenant_id',
        'user_id',
        'masterbarang_id',
        'tgl_beli',
        'tgl_jual',
        'harga_beli',
        'harga_jual',
        'biaya',
        'status',
        'unit_titip',
        'gudang_id',
        'share_token',
        'share_token_expires_at',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function masterbarang(): BelongsTo
    {
        return $this->belongsTo(Masterbarang::class);
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    public function unitdetail(): HasOne
    {
        return $this->hasOne(Unitdetail::class);
    }

    public function unitbiayas(): HasMany
    {
        return $this->hasMany(Unitbiaya::class);
    }

    public function gambars(): HasMany
    {
        return $this->hasMany(Gambar::class);
    }

    protected function casts(): array
    {
        return [
            'tgl_beli' => 'date',
            'tgl_jual' => 'date',
            'unit_titip' => 'boolean',
            'share_token_expires_at' => 'datetime',
        ];
    }
}
