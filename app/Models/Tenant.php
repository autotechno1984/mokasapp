<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant
{
    use HasDomains;

    protected $fillable = [
        'id',
        'tenantkey',
        'nama_tenant',
        'subdomain',
        'jenis_usaha',
        'status',
        'plan_id',
        'settings',
        'data',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'tenantkey',
            'nama_tenant',
            'subdomain',
            'jenis_usaha',
            'status',
            'plan_id',
            'settings',
            'data',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $tenant) {
            if (! $tenant->tenantkey) {
                $tenant->tenantkey = (string) Str::uuid();
            }
        });

        static::created(function (self $tenant) {
            if ($tenant->subdomain) {
                $tenant->domains()->create([
                    'domain' => $tenant->subdomain . '.' . config('app.domain'),
                ]);
            }
        });

        static::updated(function (self $tenant) {
            if ($tenant->wasChanged('subdomain') && $tenant->subdomain) {
                $tenant->domains()->delete();
                $tenant->domains()->create([
                    'domain' => $tenant->subdomain . '.' . config('app.domain'),
                ]);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'tenantkey';
    }

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'data' => 'array',
            'plan_id' => 'integer',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
