<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Feature extends Model
{
    use CentralConnection;

    protected $fillable = ['name', 'action', 'number', 'plan_id'];

    public function setConnection($name): Feature
    {
        return parent::setConnection($this->getConnectionName());
    }

    public function plan(): BelongsTo
    {
        return $this->BelongsTo(Plan::class, 'plan_id', 'id');
    }
}
