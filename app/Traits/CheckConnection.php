<?php

namespace App\Traits;

trait CheckConnection
{
    /**
     * Check if the model is in the central database.
     */
    public function isInCentralDatabase(): bool
    {
        return $this->getConnectionName() === config('tenancy.database.central_connection');
    }

    /**
     * Check if the model is in a tenant database.
     */
    public function isInTenantDatabase(): bool
    {
        return $this->getConnectionName() === 'tenant';
    }
}
