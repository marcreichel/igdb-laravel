<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Tests;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use MarcReichel\IGDBLaravel\IGDBLaravelServiceProvider;
use MarcReichel\IGDBLaravel\Models\Webhook;
use Orchestra\Testbench\TestCase as Orchestra;
use ReflectionClass;

/**
 * @internal
 */
class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        Route::post(
            'igdb-webhook/handle/{model}/{method}',
            static fn (\Illuminate\Http\Request $request) => Webhook::handle($request),
        )->name('handle-igdb-webhook');
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

    protected function isApiCall(Request $request, string $endpoint, string $requestBody): bool
    {
        return Str::startsWith($request->url(), 'https://api.igdb.com/v4/' . $endpoint)
            && Str::of($request->body())->contains($requestBody);
    }

    protected function isWebhookCall(Request $request, string $endpoint): bool
    {
        return $request->url() === 'https://api.igdb.com/v4/' . $endpoint . '/webhooks' && $request->isForm();
    }

    protected function createWebhookResponse(Request $request): PromiseInterface
    {
        $data = $request->data();
        $subCategory = null;
        switch ($data['method']) {
            case 'create':
                $subCategory = 0;

                break;
            case 'delete':
                $subCategory = 1;

                break;
            case 'update':
                $subCategory = 2;

                break;
        }

        return Http::response([
            'id' => 1337,
            'url' => $data['url'],
            'category' => 1,
            'sub_category' => $subCategory,
            'active' => true,
            'secret' => $data['secret'],
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public static function modelsDataProvider(): array
    {
        $files = glob(__DIR__ . '/../src/Models/*.php');
        $classNames = [];
        $blackList = ['PopularityPrimitive', 'Search', 'Webhook'];

        if (!$files) {
            return $classNames;
        }

        foreach ($files as $file) {
            $classString = 'MarcReichel\IGDBLaravel\Models\\' . basename($file, '.php');
            if (!class_exists($classString)) {
                continue;
            }
            $reflection = new ReflectionClass($classString);
            if ($reflection->isAbstract()) {
                continue;
            }
            if (in_array(class_basename($classString), $blackList)) {
                continue;
            }

            $classBasename = class_basename($classString);

            $classNames[$classBasename] = [$classBasename];
        }

        return $classNames;
    }
}
