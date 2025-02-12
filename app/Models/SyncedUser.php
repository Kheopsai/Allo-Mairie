<?php

namespace App\Models;

use App\Traits\CheckConnection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laratrust\Contracts\LaratrustUser;
use Stancl\Tenancy\Contracts\Syncable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\HasRolesAndPermissions;
use Stancl\Tenancy\Database\Concerns\ResourceSyncing;

class SyncedUser extends Authenticatable implements LaratrustUser, Syncable
{
    use HasRolesAndPermissions;
    use CheckConnection;
    use ResourceSyncing;

    public $incrementing = false;

    public $appends = [
        'profile_photo_url',
    ];

    protected $guarded = [];

    public $timestamps = false;

    public $table = 'users';

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'password',
        'name',
        'metropole_id',
        'global_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected function FullName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->first_name . ' ' . $this->last_name,
        );
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
        return User::class;
    }

    public function getSyncedAttributeNames(): array
    {
        return [
            'first_name',
            'last_name',
            'password',
            'email',
            'metropole_id',
        ];
    }

    public function channels():HasMany
    {
        return $this->hasMany(Channel::class);
    }
}
