<?php

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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

    protected function isApiCall(Request $request, string $requestBody): bool
    {
        return Str::startsWith($request->url(), 'https://api.igdb.com/v4/')
            && Str::of($request->body())->contains($requestBody);
    }
}
