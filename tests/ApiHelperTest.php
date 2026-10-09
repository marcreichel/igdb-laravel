<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use MarcReichel\IGDBLaravel\ApiHelper;
use MarcReichel\IGDBLaravel\Builder;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
class ApiHelperTest extends TestCase
{
    /**
     * @throws AuthenticationException
     */
    public function testItShouldUseAccessTokenFromCache(): void
    {
        Cache::put('igdb_cache.access_token', 'some-token');

        $token = ApiHelper::retrieveAccessToken();

        $this->assertEquals('some-token', $token);
    }

    /**
     * @throws AuthenticationException
     */
    public function testItShouldRetrieveAccessTokenFromTwitch(): void
    {
        Cache::forget('igdb_cache.access_token');

        Http::fake([
            '*/oauth2/token*' => Http::response([
                'access_token' => 'test-suite-token',
                'expires_in' => 3600,
            ]),
        ]);

        $token = ApiHelper::retrieveAccessToken();

        $this->assertEquals('test-suite-token', $token);
    }

    /**
     * @throws AuthenticationException
     */
    public function testItShouldThrowAuthenticationException(): void
    {
        $this->expectException(AuthenticationException::class);

        Cache::forget('igdb_cache.access_token');

        Http::fake([
            '*/oauth2/token*' => Http::response([], Response::HTTP_INTERNAL_SERVER_ERROR),
        ]);

        ApiHelper::retrieveAccessToken();
    }

    public function testItShouldRefreshRejectedAccessTokenAndRetry(): void
    {
        Cache::put('igdb_cache.access_token', 'expired-token');

        Http::fake([
            '*/oauth2/token*' => Http::response([
                'access_token' => 'fresh-token',
                'expires_in' => 3600,
            ]),
            '*/games' => Http::sequence()
                ->push([], Response::HTTP_UNAUTHORIZED)
                ->push([['id' => 1337]]),
        ]);

        $games = (new Builder('games'))->cache(0)->get();

        $this->assertEquals(1337, $games[0]['id']);
        $this->assertEquals('fresh-token', Cache::get('igdb_cache.access_token'));
        Http::assertSent(static fn (Request $request) => $request->hasHeader('Authorization', 'Bearer fresh-token'));
    }

    public function testItShouldStopAfterConfiguredRetries(): void
    {
        Cache::put('igdb_cache.access_token', 'some-token');

        Http::fake([
            '*/games' => Http::response([], Response::HTTP_INTERNAL_SERVER_ERROR),
        ]);

        try {
            (new Builder('games'))->cache(0)->retries(2)->get();
            $this->fail('Expected RequestException.');
        } catch (RequestException) {
            Http::assertSentCount(2);
        }
    }
}
