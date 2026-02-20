<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (! app()->bound('currentTenant')) {
                return;
            }

            $tenant = app('currentTenant');
            if ($tenant) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenant->getTenantKey());
            }
        });

        static::creating(function (Model $model) {
            if (! app()->bound('currentTenant')) {
                return;
            }

            $tenant = app('currentTenant');
            if ($tenant && ! $model->tenant_id) {
                $model->tenant_id = $tenant->getTenantKey();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
