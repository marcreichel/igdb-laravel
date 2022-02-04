<?php

namespace MarcReichel\IGDBLaravel;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;

class ApiHelper
{
    public const IGDB_BASE_URI = 'https://api.igdb.com/v4/';

    /**
     * Retrieves an Access Token from Twitch.
     *
     * @return string
     * @throws AuthenticationException
     */
    public static function retrieveAccessToken(): string
    {
        $accessTokenCacheKey = 'igdb_cache.access_token';

        $accessToken = Cache::get($accessTokenCacheKey, false);

        if ($accessToken && is_string($accessToken)) {
            return $accessToken;
        }

        try {
            $query = http_build_query([
                'client_id' => config('igdb.credentials.client_id'),
                'client_secret' => config('igdb.credentials.client_secret'),
                'grant_type' => 'client_credentials',
            ]);
            $response = Http::post('https://id.twitch.tv/oauth2/token?' . $query)
                ->throw()
                ->json();

            if (is_array($response) && isset($response['access_token']) && $response['expires_in']) {
                Cache::put($accessTokenCacheKey, (string)$response['access_token'], (int)$response['expires_in'] - 60);

                $accessToken = $response['access_token'];
            }
        } catch (Exception) {
            throw new AuthenticationException('Access Token could not be retrieved from Twitch.');
        }

        return (string) $accessToken;
    }
}
