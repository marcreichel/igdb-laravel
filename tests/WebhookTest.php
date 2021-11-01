<?php

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Events\GameCreated;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookSecretException;
use MarcReichel\IGDBLaravel\Models\Artwork;
use MarcReichel\IGDBLaravel\Models\Company;
use MarcReichel\IGDBLaravel\Models\Game;

class WebhookTest extends TestCase
{
    /** @test */
    public function it_should_generate_webhook(): void
    {
        $webhook = Game::createWebhook(Method::CREATE);

        Http::assertSent(function (Request $request) {
            return $this->isWebhookCall($request, 'games');
        });

        self::assertEquals(0, $webhook->sub_category);
        self::assertEquals('http://localhost/igdb-webhook/handle/games/create', $webhook->url);

        $webhook = Company::createWebhook(Method::UPDATE);

        Http::assertSent(function (Request $request) {
            return $this->isWebhookCall($request, 'companies');
        });

        self::assertEquals(2, $webhook->sub_category);
        self::assertEquals('http://localhost/igdb-webhook/handle/companies/update', $webhook->url);

        $webhook = Artwork::createWebhook(Method::DELETE);

        Http::assertSent(function (Request $request) {
            return $this->isWebhookCall($request, 'artworks');
        });

        self::assertEquals(1, $webhook->sub_category);
        self::assertEquals('http://localhost/igdb-webhook/handle/artworks/delete', $webhook->url);
    }

    /** @test */
    public function it_should_receive_webhook_calls_and_trigger_event(): void
    {
        $url = 'igdb-webhook/handle/games/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameCreated::class);
    }

    /** @test */
    public function it_should_validate_webhook_secret(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(InvalidWebhookSecretException::class);

        $url = 'igdb-webhook/handle/games/create';

        $this->withHeaders([
            'X-Secret' => 'foobar',
        ])
            ->postJson($url, ['id' => 1337]);

        Event::assertNotDispatched(GameCreated::class);
    }
}
