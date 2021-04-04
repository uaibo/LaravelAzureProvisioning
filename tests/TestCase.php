<?php

namespace RobTrehy\LaravelAzureProvisioning\Tests;

use RobTrehy\LaravelAzureProvisioning\AzureProvisioningProvider;
use RobTrehy\LaravelAzureProvisioning\Tests\Models\User;
use RobTrehy\LaravelAzureProvisioning\Tests\Models\Group;

class TestCase extends \Orchestra\Testbench\TestCase
{

    protected $routePrefix;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routePrefix = config('azureprovisioning.routePrefix');

        config(['azureprovisioning.Users.model' => User::class]);
        config(['azureprovisioning.Groups.model' => Group::class]);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            AzureProvisioningProvider::class,
        ];
    }

    /**
     * Ignore package discovery from.
     *
     * @return array
     */
    public function ignorePackageDiscoveriesFrom()
    {
        return [];
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
