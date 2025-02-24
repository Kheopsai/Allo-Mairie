<?php

namespace App\Models;

use Laravel\Cashier\Subscription as CachierSubscription;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Subscription extends CachierSubscription
{
    use CentralConnection;

    public function setConnection($name): Subscription
    {
        return parent::setConnection($this->getConnectionName());
    }
}
