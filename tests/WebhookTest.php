<?php

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
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
        $webhook = Game::createWebhook('http://localhost/webhook/handle', 'create');

        Http::assertSent(function (Request $request) {
            return $this->isWebhookCall($request);
        });

        self::assertEquals(0, $webhook->sub_category);
        self::assertEquals('http://localhost/webhook/handle?x_igdb_endpoint=games&x_igdb_method=create', $webhook->url);

        $webhook = Company::createWebhook('http://localhost/webhook/handle', 'update');

        Http::assertSent(function (Request $request) {
            return $this->isWebhookCall($request);
        });

        self::assertEquals(2, $webhook->sub_category);
        self::assertEquals('http://localhost/webhook/handle?x_igdb_endpoint=companies&x_igdb_method=update', $webhook->url);

        $webhook = Artwork::createWebhook('http://localhost/webhook/handle', 'delete');

        Http::assertSent(function (Request $request) {
            return $this->isWebhookCall($request);
        });

        self::assertEquals(1, $webhook->sub_category);
        self::assertEquals('http://localhost/webhook/handle?x_igdb_endpoint=artworks&x_igdb_method=delete', $webhook->url);
    }

    /** @test */
    public function it_should_receive_webhook_calls_and_trigger_event(): void
    {
        $url = 'webhook/handle?x_igdb_endpoint=games&x_igdb_method=create';

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

        $url = 'webhook/handle?x_igdb_endpoint=games&x_igdb_method=create';

        $this->withHeaders([
            'X-Secret' => 'foobar',
        ])
            ->postJson($url, ['id' => 1337]);

        Event::assertNotDispatched(GameCreated::class);
    }
}
