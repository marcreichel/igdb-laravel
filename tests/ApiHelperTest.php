<?php

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Support\Facades\Cache;
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

        $token = ApiHelper::retrieveAccessToken();

        $this->assertEquals('test-suite-token', $token);
    }
}
