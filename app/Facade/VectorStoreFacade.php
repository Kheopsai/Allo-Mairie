<?php

namespace App\Facade;

use App\Interface\VectorStoreInterface;
use Exception;

class VectorStoreFacade
{
    protected static array $instances = [];

    /**
     * @throws Exception
     */
    public static function boot(): void
    {
        $providers = config('vectorstores.providers');
        foreach ($providers as $name => $class) {
            if (! class_exists($class)) {
                throw new Exception("Integration service provider class {$class} does not exist.");
            }
            self::$instances[$name] = new $class();
        }
    }

    /**
     * @throws Exception
     */
    public static function build(string $providerName): VectorStoreInterface
    {
        if (! isset(self::$instances[$providerName])) {
            throw new Exception("Service provider '{$providerName}' is not registered.");
        }

        return self::$instances[$providerName];
    }
}
