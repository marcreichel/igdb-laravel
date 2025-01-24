<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use MarcReichel\IGDBLaravel\Enums\Webhook\Category;
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookMethodException;
use MarcReichel\IGDBLaravel\Exceptions\WebhookSecretMissingException;
use MarcReichel\IGDBLaravel\Models\Artwork;
use MarcReichel\IGDBLaravel\Models\Company;
use MarcReichel\IGDBLaravel\Models\Game;
use MarcReichel\IGDBLaravel\Models\Webhook;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
class WebhookTest extends TestCase
{
    private string $hash;
    private string $prefix;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hash = substr(md5((string) config('igdb.credentials.client_id')), 0, 8);
        $this->prefix = 'igdb-webhook/handle/' . $this->hash;

        Cache::put('igdb_cache.access_token', 'some-token');

        Http::fake([
            '*/oauth2/token*' => Http::response([
                'access_token' => 'test-suite-token',
                'expires_in' => 3600,
            ]),
            '*/games/webhooks' => fn (Request $request) => $this->createWebhookResponse($request),
            '*/companies/webhooks' => fn (Request $request) => $this->createWebhookResponse($request),
            '*/artworks/webhooks' => fn (Request $request) => $this->createWebhookResponse($request),
            '*/webhooks' => Http::response(),
            '*/count' => Http::response(['count' => 1337]),
            '*/companies' => Http::response(['id' => 1337, 'name' => 'Fortnite']),
            '*' => Http::response(),
        ]);
    }

    public function testItShouldGenerateWebhook(): void
    {
        $webhook = Game::createWebhook(Method::CREATE);

        Http::assertSent(fn (Request $request) => $this->isWebhookCall($request, 'games'));

        self::assertEquals(0, $webhook->sub_category);
        self::assertEquals('http://localhost/' . $this->prefix . '/games/create', $webhook->url);

        $webhook = Company::createWebhook(Method::UPDATE);

        Http::assertSent(fn (Request $request) => $this->isWebhookCall($request, 'companies'));

        self::assertEquals(2, $webhook->sub_category);
        self::assertEquals('http://localhost/' . $this->prefix . '/companies/update', $webhook->url);

        $webhook = Artwork::createWebhook(Method::DELETE);

        Http::assertSent(fn (Request $request) => $this->isWebhookCall($request, 'artworks'));

        self::assertEquals(1, $webhook->sub_category);
        self::assertEquals('http://localhost/' . $this->prefix . '/artworks/delete', $webhook->url);
    }

    public function testItShouldGenerateWebhookWithStringMethod(): void
    {
        $webhook = Game::createWebhook('create');

        Http::assertSent(fn (Request $request) => $this->isWebhookCall($request, 'games'));

        self::assertEquals(0, $webhook->sub_category);
        self::assertEquals('http://localhost/' . $this->prefix . '/games/create', $webhook->url);

        $webhook = Company::createWebhook('update');

        Http::assertSent(fn (Request $request) => $this->isWebhookCall($request, 'companies'));

        self::assertEquals(2, $webhook->sub_category);
        self::assertEquals('http://localhost/' . $this->prefix . '/companies/update', $webhook->url);

        $webhook = Artwork::createWebhook('delete');

        Http::assertSent(fn (Request $request) => $this->isWebhookCall($request, 'artworks'));

        self::assertEquals(1, $webhook->sub_category);
        self::assertEquals('http://localhost/' . $this->prefix . '/artworks/delete', $webhook->url);
    }

    public function testItShouldThrowExceptionWithInvalidMethod(): void
    {
        $this->expectException(InvalidWebhookMethodException::class);

        Game::createWebhook('foo');
    }

    public function testItShouldThrowExceptionWhenNoSecretIsSet(): void
    {
        $this->expectException(WebhookSecretMissingException::class);

        config(['igdb.webhook_secret' => null]);

        Game::createWebhook(Method::CREATE);
    }

    #[DataProvider('modelsDataProvider')]
    public function testItShouldDispatchCreatedEvent(string $className): void
    {
        $eventClassString = 'MarcReichel\IGDBLaravel\Events\\' . $className . 'Created';
        $url = $this->prefix . '/' . Str::snake($className) . '/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched($eventClassString);
    }

    #[DataProvider('modelsDataProvider')]
    public function testItShouldDispatchUpdatedEvent(string $className): void
    {
        $eventClassString = 'MarcReichel\IGDBLaravel\Events\\' . $className . 'Updated';
        $url = $this->prefix . '/' . Str::snake($className) . '/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched($eventClassString);
    }

    #[DataProvider('modelsDataProvider')]
    public function testItShouldDispatchDeletedEvent(string $className): void
    {
        $eventClassString = 'MarcReichel\IGDBLaravel\Events\\' . $className . 'Deleted';
        $url = $this->prefix . '/' . Str::snake($className) . '/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched($eventClassString);
    }

    #[DataProvider('modelsDataProvider')]
    public function testItShouldHaveACategoryCaseForEveryModel(string $className): void
    {
        $categories = collect(Category::cases())
            ->map(static fn (Category $category) => $category->name)
            ->values()
            ->toArray();

        $this->assertContains($className, $categories);
    }

    public function testItShouldListAllWebhooks(): void
    {
        Webhook::all();

        Http::assertSent(static fn (Request $request) => $request->url() === 'https://api.igdb.com/v4/webhooks');
    }

    public function testItShouldFindAWebhook(): void
    {
        Webhook::find(1337);

        Http::assertSent(static fn (Request $request) => $request->url() === 'https://api.igdb.com/v4/webhooks/1337');
    }
}
