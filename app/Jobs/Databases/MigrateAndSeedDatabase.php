<?php

namespace App\Jobs\Databases;

use App\Enums\RoleEnum;
use App\Enums\TenantEnum;
use App\Models\Role;
use App\Models\SyncedUser;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Events\DatabaseMigrated;
use Throwable;

class MigrateAndSeedDatabase implements ShouldBeUnique, ShouldQueue
{
    use CentralConnection;
    use Dispatchable,InteractsWithQueue, Queueable,SerializesModels;

    protected TenantWithDatabase $tenant;
    /**
     * Create a new job instance.
     */
    public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant=$tenant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $databaseName = $this->tenant->database()->getName();

            if (! $this->databaseExists($databaseName)) {
                $this->createDatabase($databaseName);
            }

            $this->migrateTenantDatabase($databaseName);

            $this->seedTenantDatabase();

            $this->tenant->update(['status' => TenantEnum::Active()]);

        } catch (Throwable $exception) {
            Log::error("the error message is ".$exception->getMessage());
            $this->resetConnection();
            throw $exception;
        } finally {
            $this->resetConnection();
            event(new DatabaseMigrated($this->tenant));
        }
    }

        /**
     * Check if the database exists.
     *
     * @throws DatabaseManagerNotRegisteredException
     */
    protected function databaseExists(string $databaseName): bool
    {
        return $this->tenant->database()->manager()->databaseExists($databaseName);
    }

    /**
     * Create the database using a raw SQL statement.
     */
    protected function createDatabase(string $databaseName): void
    {
        Schema::setConnection(config('database.default'))->createDatabase($databaseName);
    }

    /**
     * Migrate the tenant's database.
     */
    protected function migrateTenantDatabase(string $databaseName): void
    {
        config(['database.connections.tenant.database' => $databaseName]);

        DB::purge('tenant');
        DB::reconnect('tenant');

        $migrator = app('migrator');
        $migrator->setConnection('tenant');

        if (! $migrator->repositoryExists()) {
            $migrator->getRepository()->createRepository();
        }

        $tenantMigrationsPath = database_path('migrations/tenant');
        $migrator->run([$tenantMigrationsPath], [
            'step' => false,
            'pretend' => false,
        ]);
    }

    /**
     * Seed the tenant's database.
     */
    protected function seedTenantDatabase(): void
    {
        $metropole = $this->tenant->metropole()->first();

        if ($metropole && $user = $metropole->users()->latest()->first()) {
            $this->tenant->run(function () use ($user) {
                Role::firstOrCreate(
                    ['name' => RoleEnum::Admin],
                    ['display_name' => ucfirst(RoleEnum::Admin)]
                );

                $attributes = $user->getAttributes();
                $tenantUser = new SyncedUser($attributes);
                $tenantUser->setRelations([]);
                $tenantUser->save();

                $tenantUser->addRole(RoleEnum::Admin);
            });
        } else {
            Log::warning("Company or user not found for tenant: {$this->tenant->id}");
        }
    }

    /**
     * Reset the tenant database connection.
     */
    public function resetConnection(): void
    {
        DB::purge('tenant');
        DB::disconnect('tenant');
        DB::reconnect(config('database.default'));
    }
}
