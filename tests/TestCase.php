<?php

namespace DigitalCreative\Dashboard\Tests;

use DigitalCreative\Dashboard\CoreDashboardServiceProvider;
use JohnDoe\BlogPackage\BlogPackageServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrations();

        $this->withFactories(__DIR__ . '/Factories');

    }

    protected function getPackageProviders($app): array
    {
        return [
            CoreDashboardServiceProvider::class,
            TestServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app[ 'config' ]->set('database.default', 'sqlite');
        $app[ 'config' ]->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Load the migrations for the test environment.
     *
     * @return void
     */
    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom([
            '--database' => 'sqlite',
            '--path' => realpath(__DIR__ . '/Migrations'),
        ]);
    }
}
