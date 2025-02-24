<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Billable;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\VirtualColumn\VirtualColumn;

/**
 * @method static whereHas(string $string, \Closure $param)
 * @method static create(array $array)
 */
class Tenant extends BaseTenant  implements TenantWithDatabase
{
    use HasDomains, HasDatabase;
    use Billable;
    use VirtualColumn;

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'stripe_id',
            'status',
            'pm_type',
            'pm_last_four',
            'trial_ends_at',
            'metropole_id',
            'created_at',
            'updated_at',
        ];
    }

    public function domain(): HasOne
    {
        return $this->hasOne(config('tenancy.domain_model'));
    }

    public function metropole(): BelongsTo
    {
        return $this->belongsTo(Metropole::class, 'metropole_id');
    }

    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class);
    }
}
