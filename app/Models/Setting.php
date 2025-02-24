<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\VirtualColumn\VirtualColumn;

class Setting extends Model
{
    use CentralConnection;
    use HasImage;
    use VirtualColumn;

    protected $guarded = [];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'tenant_id',
            'created_at',
            'updated_at',
        ];
    }


    public function setConnection($name): Setting
    {
        return parent::setConnection($this->getConnectionName());
    }
}
