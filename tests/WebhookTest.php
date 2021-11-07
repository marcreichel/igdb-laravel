<?php

namespace MarcReichel\IGDBLaravel\Tests;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Events\AgeRatingContentDescriptionCreated;
use MarcReichel\IGDBLaravel\Events\AgeRatingContentDescriptionDeleted;
use MarcReichel\IGDBLaravel\Events\AgeRatingContentDescriptionUpdated;
use MarcReichel\IGDBLaravel\Events\AgeRatingCreated;
use MarcReichel\IGDBLaravel\Events\AgeRatingDeleted;
use MarcReichel\IGDBLaravel\Events\AgeRatingUpdated;
use MarcReichel\IGDBLaravel\Events\AlternativeNameCreated;
use MarcReichel\IGDBLaravel\Events\AlternativeNameDeleted;
use MarcReichel\IGDBLaravel\Events\AlternativeNameUpdated;
use MarcReichel\IGDBLaravel\Events\ArtworkCreated;
use MarcReichel\IGDBLaravel\Events\ArtworkDeleted;
use MarcReichel\IGDBLaravel\Events\ArtworkUpdated;
use MarcReichel\IGDBLaravel\Events\CharacterCreated;
use MarcReichel\IGDBLaravel\Events\CharacterDeleted;
use MarcReichel\IGDBLaravel\Events\CharacterMugShotCreated;
use MarcReichel\IGDBLaravel\Events\CharacterMugShotDeleted;
use MarcReichel\IGDBLaravel\Events\CharacterMugShotUpdated;
use MarcReichel\IGDBLaravel\Events\CharacterUpdated;
use MarcReichel\IGDBLaravel\Events\CollectionCreated;
use MarcReichel\IGDBLaravel\Events\CollectionDeleted;
use MarcReichel\IGDBLaravel\Events\CollectionUpdated;
use MarcReichel\IGDBLaravel\Events\CompanyCreated;
use MarcReichel\IGDBLaravel\Events\CompanyDeleted;
use MarcReichel\IGDBLaravel\Events\CompanyLogoCreated;
use MarcReichel\IGDBLaravel\Events\CompanyLogoDeleted;
use MarcReichel\IGDBLaravel\Events\CompanyLogoUpdated;
use MarcReichel\IGDBLaravel\Events\CompanyUpdated;
use MarcReichel\IGDBLaravel\Events\CompanyWebsiteCreated;
use MarcReichel\IGDBLaravel\Events\CompanyWebsiteDeleted;
use MarcReichel\IGDBLaravel\Events\CompanyWebsiteUpdated;
use MarcReichel\IGDBLaravel\Events\CoverCreated;
use MarcReichel\IGDBLaravel\Events\CoverDeleted;
use MarcReichel\IGDBLaravel\Events\CoverUpdated;
use MarcReichel\IGDBLaravel\Events\ExternalGameCreated;
use MarcReichel\IGDBLaravel\Events\ExternalGameDeleted;
use MarcReichel\IGDBLaravel\Events\ExternalGameUpdated;
use MarcReichel\IGDBLaravel\Events\FranchiseCreated;
use MarcReichel\IGDBLaravel\Events\FranchiseDeleted;
use MarcReichel\IGDBLaravel\Events\FranchiseUpdated;
use MarcReichel\IGDBLaravel\Events\GameCreated;
use MarcReichel\IGDBLaravel\Events\GameDeleted;
use MarcReichel\IGDBLaravel\Events\GameEngineCreated;
use MarcReichel\IGDBLaravel\Events\GameEngineDeleted;
use MarcReichel\IGDBLaravel\Events\GameEngineLogoCreated;
use MarcReichel\IGDBLaravel\Events\GameEngineLogoDeleted;
use MarcReichel\IGDBLaravel\Events\GameEngineLogoUpdated;
use MarcReichel\IGDBLaravel\Events\GameEngineUpdated;
use MarcReichel\IGDBLaravel\Events\GameModeCreated;
use MarcReichel\IGDBLaravel\Events\GameModeDeleted;
use MarcReichel\IGDBLaravel\Events\GameModeUpdated;
use MarcReichel\IGDBLaravel\Events\GameUpdated;
use MarcReichel\IGDBLaravel\Events\GameVersionCreated;
use MarcReichel\IGDBLaravel\Events\GameVersionDeleted;
use MarcReichel\IGDBLaravel\Events\GameVersionFeatureCreated;
use MarcReichel\IGDBLaravel\Events\GameVersionFeatureDeleted;
use MarcReichel\IGDBLaravel\Events\GameVersionFeatureUpdated;
use MarcReichel\IGDBLaravel\Events\GameVersionFeatureValueCreated;
use MarcReichel\IGDBLaravel\Events\GameVersionFeatureValueDeleted;
use MarcReichel\IGDBLaravel\Events\GameVersionFeatureValueUpdated;
use MarcReichel\IGDBLaravel\Events\GameVersionUpdated;
use MarcReichel\IGDBLaravel\Events\GameVideoCreated;
use MarcReichel\IGDBLaravel\Events\GameVideoDeleted;
use MarcReichel\IGDBLaravel\Events\GameVideoUpdated;
use MarcReichel\IGDBLaravel\Events\GenreCreated;
use MarcReichel\IGDBLaravel\Events\GenreDeleted;
use MarcReichel\IGDBLaravel\Events\GenreUpdated;
use MarcReichel\IGDBLaravel\Events\InvolvedCompanyCreated;
use MarcReichel\IGDBLaravel\Events\InvolvedCompanyDeleted;
use MarcReichel\IGDBLaravel\Events\InvolvedCompanyUpdated;
use MarcReichel\IGDBLaravel\Events\KeywordCreated;
use MarcReichel\IGDBLaravel\Events\KeywordDeleted;
use MarcReichel\IGDBLaravel\Events\KeywordUpdated;
use MarcReichel\IGDBLaravel\Events\MultiplayerModeCreated;
use MarcReichel\IGDBLaravel\Events\MultiplayerModeDeleted;
use MarcReichel\IGDBLaravel\Events\MultiplayerModeUpdated;
use MarcReichel\IGDBLaravel\Events\PlatformCreated;
use MarcReichel\IGDBLaravel\Events\PlatformDeleted;
use MarcReichel\IGDBLaravel\Events\PlatformFamilyCreated;
use MarcReichel\IGDBLaravel\Events\PlatformFamilyDeleted;
use MarcReichel\IGDBLaravel\Events\PlatformFamilyUpdated;
use MarcReichel\IGDBLaravel\Events\PlatformLogoCreated;
use MarcReichel\IGDBLaravel\Events\PlatformLogoDeleted;
use MarcReichel\IGDBLaravel\Events\PlatformLogoUpdated;
use MarcReichel\IGDBLaravel\Events\PlatformUpdated;
use MarcReichel\IGDBLaravel\Events\PlatformVersionCompanyCreated;
use MarcReichel\IGDBLaravel\Events\PlatformVersionCompanyDeleted;
use MarcReichel\IGDBLaravel\Events\PlatformVersionCompanyUpdated;
use MarcReichel\IGDBLaravel\Events\PlatformVersionCreated;
use MarcReichel\IGDBLaravel\Events\PlatformVersionDeleted;
use MarcReichel\IGDBLaravel\Events\PlatformVersionReleaseDateCreated;
use MarcReichel\IGDBLaravel\Events\PlatformVersionReleaseDateDeleted;
use MarcReichel\IGDBLaravel\Events\PlatformVersionReleaseDateUpdated;
use MarcReichel\IGDBLaravel\Events\PlatformVersionUpdated;
use MarcReichel\IGDBLaravel\Events\PlatformWebsiteCreated;
use MarcReichel\IGDBLaravel\Events\PlatformWebsiteDeleted;
use MarcReichel\IGDBLaravel\Events\PlatformWebsiteUpdated;
use MarcReichel\IGDBLaravel\Events\PlayerPerspectiveCreated;
use MarcReichel\IGDBLaravel\Events\PlayerPerspectiveDeleted;
use MarcReichel\IGDBLaravel\Events\PlayerPerspectiveUpdated;
use MarcReichel\IGDBLaravel\Events\ReleaseDateCreated;
use MarcReichel\IGDBLaravel\Events\ReleaseDateDeleted;
use MarcReichel\IGDBLaravel\Events\ReleaseDateUpdated;
use MarcReichel\IGDBLaravel\Events\ScreenshotCreated;
use MarcReichel\IGDBLaravel\Events\ScreenshotDeleted;
use MarcReichel\IGDBLaravel\Events\ScreenshotUpdated;
use MarcReichel\IGDBLaravel\Events\ThemeCreated;
use MarcReichel\IGDBLaravel\Events\ThemeDeleted;
use MarcReichel\IGDBLaravel\Events\ThemeUpdated;
use MarcReichel\IGDBLaravel\Events\WebsiteCreated;
use MarcReichel\IGDBLaravel\Events\WebsiteDeleted;
use MarcReichel\IGDBLaravel\Events\WebsiteUpdated;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookMethodException;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookSecretException;
use MarcReichel\IGDBLaravel\Exceptions\WebhookSecretMissingException;
use MarcReichel\IGDBLaravel\Models\Artwork;
use MarcReichel\IGDBLaravel\Models\Company;
use MarcReichel\IGDBLaravel\Models\Game;

class WebhookTest extends TestCase
{
    private string $hash;
    private string $prefix;

    public function setUp(): void
    {
        parent::setUp();

        $this->hash = substr(md5(config('igdb.credentials.client_id')), 0, 8);
        $this->prefix = 'igdb-webhook/handle/' . $this->hash;

        Cache::put('igdb_cache.access_token', 'some-token');

        Http::fake([
            '*/oauth2/token*' => Http::response([
                'access_token' => 'test-suite-token',
                'expires_in' => 3600
            ]),
            '*/games/webhooks' => function (Request $request) {
                return $this->createWebhookResponse($request);
            },
            '*/companies/webhooks' => function (Request $request) {
                return $this->createWebhookResponse($request);
            },
            '*/artworks/webhooks' => function (Request $request) {
                return $this->createWebhookResponse($request);
            },
            '*/webhooks' => Http::response(),
            '*/count' => Http::response(['count' => 1337]),
            '*/companies' => Http::response(['id' => 1337, 'name' => 'Fortnite']),
            '*' => Http::response(),
        ]);
    }

    /** @test */
    public function it_should_generate_webhook(): void
    {
        $webhook = Game::createWebhook(Method::CREATE);

        Http::assertSent(function (Request $request) {
            return $this->isWebhookCall($request, 'games');
        });

        self::assertEquals(0, $webhook->sub_category);
        self::assertEquals('http://localhost/' . $this->prefix . '/games/create', $webhook->url);

        $webhook = Company::createWebhook(Method::UPDATE);

        Http::assertSent(function (Request $request) {
            return $this->isWebhookCall($request, 'companies');
        });

        self::assertEquals(2, $webhook->sub_category);
        self::assertEquals('http://localhost/' . $this->prefix . '/companies/update', $webhook->url);

        $webhook = Artwork::createWebhook(Method::DELETE);

        Http::assertSent(function (Request $request) {
            return $this->isWebhookCall($request, 'artworks');
        });

        self::assertEquals(1, $webhook->sub_category);
        self::assertEquals('http://localhost/' . $this->prefix . '/artworks/delete', $webhook->url);
    }

    /** @test */
    public function it_should_throw_exception_for_invalid_webhook_method(): void
    {
        $this->expectException(InvalidWebhookMethodException::class);

        Game::createWebhook('foo');
    }

    /** @test */
    public function it_should_throw_exception_when_no_secret_is_set(): void
    {
        $this->expectException(WebhookSecretMissingException::class);

        config(['igdb.webhook_secret' => null]);

        Game::createWebhook(Method::CREATE);
    }

    /** @test */
    public function it_should_dispatch_age_rating_content_destription_created_event(): void
    {
        $url = $this->prefix . '/age_rating_content_description/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(AgeRatingContentDescriptionCreated::class);
    }

    /** @test */
    public function it_should_dispatch_age_rating_content_destription_deleted_event(): void
    {
        $url = $this->prefix . '/age_rating_content_description/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(AgeRatingContentDescriptionDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_age_rating_content_destription_updated_event(): void
    {
        $url = $this->prefix . '/age_rating_content_description/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(AgeRatingContentDescriptionUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_age_rating_created_event(): void
    {
        $url = $this->prefix . '/age_rating/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(AgeRatingCreated::class);
    }

    /** @test */
    public function it_should_dispatch_age_rating_deleted_event(): void
    {
        $url = $this->prefix . '/age_rating/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(AgeRatingDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_age_rating_updated_event(): void
    {
        $url = $this->prefix . '/age_rating/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(AgeRatingUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_alternative_name_created_event(): void
    {
        $url = $this->prefix . '/alternative_name/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(AlternativeNameCreated::class);
    }

    /** @test */
    public function it_should_dispatch_alternative_name_deleted_event(): void
    {
        $url = $this->prefix . '/alternative_name/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(AlternativeNameDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_alternative_name_updated_event(): void
    {
        $url = $this->prefix . '/alternative_name/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(AlternativeNameUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_artwork_created_event(): void
    {
        $url = $this->prefix . '/artwork/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ArtworkCreated::class);
    }

    /** @test */
    public function it_should_dispatch_artwork_deleted_event(): void
    {
        $url = $this->prefix . '/artwork/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ArtworkDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_artwork_updated_event(): void
    {
        $url = $this->prefix . '/artwork/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ArtworkUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_character_created_event(): void
    {
        $url = $this->prefix . '/character/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CharacterCreated::class);
    }

    /** @test */
    public function it_should_dispatch_character_deleted_event(): void
    {
        $url = $this->prefix . '/character/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CharacterDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_character_updated_event(): void
    {
        $url = $this->prefix . '/character/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CharacterUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_character_mug_shot_created_event(): void
    {
        $url = $this->prefix . '/character_mug_shot/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CharacterMugShotCreated::class);
    }

    /** @test */
    public function it_should_dispatch_character_mug_shot_deleted_event(): void
    {
        $url = $this->prefix . '/character_mug_shot/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CharacterMugShotDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_character_mug_shot_updated_event(): void
    {
        $url = $this->prefix . '/character_mug_shot/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CharacterMugShotUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_collection_created_event(): void
    {
        $url = $this->prefix . '/collection/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CollectionCreated::class);
    }

    /** @test */
    public function it_should_dispatch_collection_deleted_event(): void
    {
        $url = $this->prefix . '/collection/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CollectionDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_collection_updated_event(): void
    {
        $url = $this->prefix . '/collection/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CollectionUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_company_created_event(): void
    {
        $url = $this->prefix . '/company/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CompanyCreated::class);
    }

    /** @test */
    public function it_should_dispatch_company_deleted_event(): void
    {
        $url = $this->prefix . '/company/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CompanyDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_company_updated_event(): void
    {
        $url = $this->prefix . '/company/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CompanyUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_company_logo_created_event(): void
    {
        $url = $this->prefix . '/company_logo/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CompanyLogoCreated::class);
    }

    /** @test */
    public function it_should_dispatch_company_logo_deleted_event(): void
    {
        $url = $this->prefix . '/company_logo/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CompanyLogoDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_company_logo_updated_event(): void
    {
        $url = $this->prefix . '/company_logo/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CompanyLogoUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_company_website_created_event(): void
    {
        $url = $this->prefix . '/company_website/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CompanyWebsiteCreated::class);
    }

    /** @test */
    public function it_should_dispatch_company_website_deleted_event(): void
    {
        $url = $this->prefix . '/company_website/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CompanyWebsiteDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_company_website_updated_event(): void
    {
        $url = $this->prefix . '/company_website/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CompanyWebsiteUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_cover_created_event(): void
    {
        $url = $this->prefix . '/cover/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CoverCreated::class);
    }

    /** @test */
    public function it_should_dispatch_cover_deleted_event(): void
    {
        $url = $this->prefix . '/cover/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CoverDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_cover_updated_event(): void
    {
        $url = $this->prefix . '/cover/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(CoverUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_external_game_created_event(): void
    {
        $url = $this->prefix . '/external_game/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ExternalGameCreated::class);
    }

    /** @test */
    public function it_should_dispatch_external_game_deleted_event(): void
    {
        $url = $this->prefix . '/external_game/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ExternalGameDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_external_game_updated_event(): void
    {
        $url = $this->prefix . '/external_game/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ExternalGameUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_franchise_created_event(): void
    {
        $url = $this->prefix . '/franchise/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(FranchiseCreated::class);
    }

    /** @test */
    public function it_should_dispatch_franchise_deleted_event(): void
    {
        $url = $this->prefix . '/franchise/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(FranchiseDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_franchise_updated_event(): void
    {
        $url = $this->prefix . '/franchise/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(FranchiseUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_game_created_event(): void
    {
        $url = $this->prefix . '/game/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameCreated::class);
    }

    /** @test */
    public function it_should_dispatch_game_deleted_event(): void
    {
        $url = $this->prefix . '/game/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_game_updated_event(): void
    {
        $url = $this->prefix . '/game/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_game_engine_created_event(): void
    {
        $url = $this->prefix . '/game_engine/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameEngineCreated::class);
    }

    /** @test */
    public function it_should_dispatch_game_engine_deleted_event(): void
    {
        $url = $this->prefix . '/game_engine/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameEngineDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_game_engine_updated_event(): void
    {
        $url = $this->prefix . '/game_engine/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameEngineUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_game_engine_logo_created_event(): void
    {
        $url = $this->prefix . '/game_engine_logo/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameEngineLogoCreated::class);
    }

    /** @test */
    public function it_should_dispatch_game_engine_logo_deleted_event(): void
    {
        $url = $this->prefix . '/game_engine_logo/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameEngineLogoDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_game_engine_logo_updated_event(): void
    {
        $url = $this->prefix . '/game_engine_logo/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameEngineLogoUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_game_mode_created_event(): void
    {
        $url = $this->prefix . '/game_mode/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameModeCreated::class);
    }

    /** @test */
    public function it_should_dispatch_game_mode_deleted_event(): void
    {
        $url = $this->prefix . '/game_mode/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameModeDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_game_mode_updated_event(): void
    {
        $url = $this->prefix . '/game_mode/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameModeUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_game_version_created_event(): void
    {
        $url = $this->prefix . '/game_version/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVersionCreated::class);
    }

    /** @test */
    public function it_should_dispatch_game_version_deleted_event(): void
    {
        $url = $this->prefix . '/game_version/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVersionDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_game_version_updated_event(): void
    {
        $url = $this->prefix . '/game_version/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVersionUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_game_version_feature_created_event(): void
    {
        $url = $this->prefix . '/game_version_feature/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVersionFeatureCreated::class);
    }

    /** @test */
    public function it_should_dispatch_game_version_feature_deleted_event(): void
    {
        $url = $this->prefix . '/game_version_feature/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVersionFeatureDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_game_version_feature_updated_event(): void
    {
        $url = $this->prefix . '/game_version_feature/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVersionFeatureUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_game_version_feature_value_created_event(): void
    {
        $url = $this->prefix . '/game_version_feature_value/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVersionFeatureValueCreated::class);
    }

    /** @test */
    public function it_should_dispatch_game_version_feature_value_deleted_event(): void
    {
        $url = $this->prefix . '/game_version_feature_value/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVersionFeatureValueDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_game_version_feature_value_updated_event(): void
    {
        $url = $this->prefix . '/game_version_feature_value/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVersionFeatureValueUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_game_video_created_event(): void
    {
        $url = $this->prefix . '/game_video/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVideoCreated::class);
    }

    /** @test */
    public function it_should_dispatch_game_video_deleted_event(): void
    {
        $url = $this->prefix . '/game_video/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVideoDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_game_video_updated_event(): void
    {
        $url = $this->prefix . '/game_video/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GameVideoUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_genre_created_event(): void
    {
        $url = $this->prefix . '/genre/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GenreCreated::class);
    }

    /** @test */
    public function it_should_dispatch_genre_deleted_event(): void
    {
        $url = $this->prefix . '/genre/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GenreDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_genre_updated_event(): void
    {
        $url = $this->prefix . '/genre/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(GenreUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_involved_company_created_event(): void
    {
        $url = $this->prefix . '/involved_company/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(InvolvedCompanyCreated::class);
    }

    /** @test */
    public function it_should_dispatch_involved_company_deleted_event(): void
    {
        $url = $this->prefix . '/involved_company/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(InvolvedCompanyDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_involved_company_updated_event(): void
    {
        $url = $this->prefix . '/involved_company/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(InvolvedCompanyUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_keyword_created_event(): void
    {
        $url = $this->prefix . '/keyword/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(KeywordCreated::class);
    }

    /** @test */
    public function it_should_dispatch_keyword_deleted_event(): void
    {
        $url = $this->prefix . '/keyword/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(KeywordDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_keyword_updated_event(): void
    {
        $url = $this->prefix . '/keyword/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(KeywordUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_multiplayer_mode_created_event(): void
    {
        $url = $this->prefix . '/multiplayer_mode/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(MultiplayerModeCreated::class);
    }

    /** @test */
    public function it_should_dispatch_multiplayer_mode_deleted_event(): void
    {
        $url = $this->prefix . '/multiplayer_mode/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(MultiplayerModeDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_multiplayer_mode_updated_event(): void
    {
        $url = $this->prefix . '/multiplayer_mode/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(MultiplayerModeUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_created_event(): void
    {
        $url = $this->prefix . '/platform/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformCreated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_deleted_event(): void
    {
        $url = $this->prefix . '/platform/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_platform_updated_event(): void
    {
        $url = $this->prefix . '/platform/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_family_created_event(): void
    {
        $url = $this->prefix . '/platform_family/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformFamilyCreated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_family_deleted_event(): void
    {
        $url = $this->prefix . '/platform_family/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformFamilyDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_platform_family_updated_event(): void
    {
        $url = $this->prefix . '/platform_family/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformFamilyUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_logo_created_event(): void
    {
        $url = $this->prefix . '/platform_logo/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformLogoCreated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_logo_deleted_event(): void
    {
        $url = $this->prefix . '/platform_logo/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformLogoDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_platform_logo_updated_event(): void
    {
        $url = $this->prefix . '/platform_logo/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformLogoUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_version_company_created_event(): void
    {
        $url = $this->prefix . '/platform_version_company/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformVersionCompanyCreated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_version_company_deleted_event(): void
    {
        $url = $this->prefix . '/platform_version_company/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformVersionCompanyDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_platform_version_company_updated_event(): void
    {
        $url = $this->prefix . '/platform_version_company/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformVersionCompanyUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_version_created_event(): void
    {
        $url = $this->prefix . '/platform_version/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformVersionCreated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_version_deleted_event(): void
    {
        $url = $this->prefix . '/platform_version/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformVersionDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_platform_version_updated_event(): void
    {
        $url = $this->prefix . '/platform_version/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformVersionUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_version_release_date_created_event(): void
    {
        $url = $this->prefix . '/platform_version_release_date/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformVersionReleaseDateCreated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_version_release_date_deleted_event(): void
    {
        $url = $this->prefix . '/platform_version_release_date/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformVersionReleaseDateDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_platform_version_release_date_updated_event(): void
    {
        $url = $this->prefix . '/platform_version_release_date/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformVersionReleaseDateUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_website_created_event(): void
    {
        $url = $this->prefix . '/platform_website/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformWebsiteCreated::class);
    }

    /** @test */
    public function it_should_dispatch_platform_website_deleted_event(): void
    {
        $url = $this->prefix . '/platform_website/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformWebsiteDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_platform_website_updated_event(): void
    {
        $url = $this->prefix . '/platform_website/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlatformWebsiteUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_player_perspective_created_event(): void
    {
        $url = $this->prefix . '/player_perspective/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlayerPerspectiveCreated::class);
    }

    /** @test */
    public function it_should_dispatch_player_perspective_deleted_event(): void
    {
        $url = $this->prefix . '/player_perspective/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlayerPerspectiveDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_player_perspective_updated_event(): void
    {
        $url = $this->prefix . '/player_perspective/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(PlayerPerspectiveUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_release_date_created_event(): void
    {
        $url = $this->prefix . '/release_date/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ReleaseDateCreated::class);
    }

    /** @test */
    public function it_should_dispatch_release_date_deleted_event(): void
    {
        $url = $this->prefix . '/release_date/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ReleaseDateDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_release_date_updated_event(): void
    {
        $url = $this->prefix . '/release_date/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ReleaseDateUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_screenshot_created_event(): void
    {
        $url = $this->prefix . '/screenshot/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ScreenshotCreated::class);
    }

    /** @test */
    public function it_should_dispatch_screenshot_deleted_event(): void
    {
        $url = $this->prefix . '/screenshot/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ScreenshotDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_screenshot_updated_event(): void
    {
        $url = $this->prefix . '/screenshot/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ScreenshotUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_theme_created_event(): void
    {
        $url = $this->prefix . '/theme/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ThemeCreated::class);
    }

    /** @test */
    public function it_should_dispatch_theme_deleted_event(): void
    {
        $url = $this->prefix . '/theme/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ThemeDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_theme_updated_event(): void
    {
        $url = $this->prefix . '/theme/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(ThemeUpdated::class);
    }

    /** @test */
    public function it_should_dispatch_website_created_event(): void
    {
        $url = $this->prefix . '/website/create';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(WebsiteCreated::class);
    }

    /** @test */
    public function it_should_dispatch_website_deleted_event(): void
    {
        $url = $this->prefix . '/website/delete';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(WebsiteDeleted::class);
    }

    /** @test */
    public function it_should_dispatch_website_updated_event(): void
    {
        $url = $this->prefix . '/website/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(WebsiteUpdated::class);
    }

    /** @test */
    public function it_should_validate_webhook_secret(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(InvalidWebhookSecretException::class);

        $url = $this->prefix . '/games/create';

        $this->withHeaders([
            'X-Secret' => 'foobar',
        ])
            ->postJson($url, ['id' => 1337]);

        Event::assertNotDispatched(GameCreated::class);
    }

    /** @test */
    public function it_should_keep_dispatching_event_for_deprecated_url(): void
    {
        $url = 'igdb-webhook/handle/website/update';

        $response = $this->withHeaders([
            'X-Secret' => 'secret',
        ])
            ->postJson($url, ['id' => 1337]);

        $response->assertStatus(200);

        Event::assertDispatched(WebsiteUpdated::class);
    }
}
