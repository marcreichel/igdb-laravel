<?php

namespace MarcReichel\IGDBLaravel\Tests;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use MarcReichel\IGDBLaravel\Models\Webhook;
use Orchestra\Testbench\TestCase as Orchestra;
use MarcReichel\IGDBLaravel\IGDBLaravelServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        Route::post('webhook/handle', function (\Illuminate\Http\Request $request) {
            return Webhook::handle($request);
        });

        Http::fake([
            '*/webhooks' => function (Request $request) {
                return $this->createWebhookResponse($request);
            },
            '*/count' => Http::response(['count' => 1337]),
            '*' => Http::response(),
        ]);
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

    protected function isWebhookCall(Request $request): bool
    {
        return Str::startsWith($request->url(), 'https://api.igdb.com/v4/') &&
            Str::endsWith($request->url(), '/webhooks') &&
            $request->isForm();
    }

    /**
     * @param Request $request
     *
     * @return PromiseInterface
     */
    private function createWebhookResponse(Request $request): PromiseInterface
    {
        $data = $request->data();
        $subCategory = 0;
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
}
