<?php

namespace App\Models;

use App\Observers\MetropoleObserver;
use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property mixed $name
 * @property mixed $id
 */
#[ObservedBy(MetropoleObserver::class)]
class Metropole extends Model
{
    use HasImage;

    protected $fillable=['name','type'];

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class,'metropole_user');
    }

    public function tenants():HasMany
    {
        return $this->hasMany(Tenant::class);
    }


    public function tenant():HasOne
    {
        return $this->hasOne(Tenant::class);
    }

}
