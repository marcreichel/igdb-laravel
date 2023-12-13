<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use MarcReichel\IGDBLaravel\ApiHelper;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;

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
}
