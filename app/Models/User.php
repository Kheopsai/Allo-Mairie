<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\CheckConnection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Stancl\Tenancy\Contracts\SyncMaster;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Concerns\ResourceSyncing;
use Stancl\Tenancy\Database\Models\TenantPivot;

class User extends Authenticatable implements LaratrustUser, SyncMaster
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use CheckConnection;
    use HasRolesAndPermissions;
    use CentralConnection;
    use ResourceSyncing;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'password',
        'metropole_id',
        'global_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function setConnection($name): User
    {
        return parent::setConnection($this->getConnectionName());
    }

    public function getTenantModelName(): string
    {
        return SyncedUser::class;
    }

    public function getGlobalIdentifierKey()
    {
        return $this->getAttribute($this->getGlobalIdentifierKeyName());
    }

    public function getGlobalIdentifierKeyName(): string
    {
        return 'global_id';
    }

    public function getCentralModelName(): string
    {
        return static::class;
    }

    public function getSyncedAttributeNames(): array
    {
        return [
            'first_name',
            'last_name',
            'password',
            'email',
            'company_id',
        ];
    }

    protected function FullName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->first_name . ' ' . $this->last_name,
        );
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_users', 'global_id', 'tenant_id', 'global_id')
            ->using(TenantPivot::class);
    }


    public function metropoles(): BelongsToMany
    {
        return $this->belongsToMany(Metropole::class,'metropole_user');
    }

    public function metropole(): BelongsTo
    {
        return $this->belongsTo(Metropole::class,'metropole_id');
    }
}
