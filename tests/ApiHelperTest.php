<?php

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use MarcReichel\IGDBLaravel\ApiHelper;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;

class ApiHelperTest extends TestCase
{
    /**
     * @test
     * @throws AuthenticationException
     */
    public function it_should_use_access_token_from_cache(): void
    {
        Cache::put('igdb_cache.access_token', 'some-token');

        $token = ApiHelper::retrieveAccessToken();

        $this->assertEquals('some-token', $token);
    }

    /**
     * @test
     * @throws AuthenticationException
     */
    public function it_should_retrieve_access_token_from_twitch(): void
    {
        Cache::forget('igdb_cache.access_token');

        Http::fake([
            '*/oauth2/token*' => Http::response([
                'access_token' => 'test-suite-token',
                'expires_in' => 3600
            ]),
        ]);

        $token = ApiHelper::retrieveAccessToken();

        $this->assertEquals('test-suite-token', $token);
    }
}
