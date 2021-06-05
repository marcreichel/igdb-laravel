<?php

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as Orchestra;
use MarcReichel\IGDBLaravel\IGDBLaravelServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Http::fake();
    }

    protected function getPackageProviders($app): array
    {
        return [
            IGDBLaravelServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // perform environment setup
    }
}
