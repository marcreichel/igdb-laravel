<?php

namespace MarcReichel\IGDBLaravel;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;

class ApiHelper
{
    /**
     * Retrieves an Access Token from Twitch.
     *
     * @return string
     * @throws AuthenticationException
     */
    public static function retrieveAccessToken(): string
    {
        $accessTokenCacheKey = 'igdb_cache.access_token';

        if ($accessToken = Cache::get($accessTokenCacheKey, false)) {
            return $accessToken;
        }

        try {
            $guzzleClient = new Client();
            $query = http_build_query([
                'client_id' => config('igdb.credentials.client_id'),
                'client_secret' => config('igdb.credentials.client_secret'),
                'grant_type' => 'client_credentials',
            ]);
            $response = json_decode($guzzleClient->post(
                'https://id.twitch.tv/oauth2/token?' . $query
            )->getBody(), true);

            if (isset($response['access_token']) && $response['expires_in']) {
                Cache::put($accessTokenCacheKey, (string)$response['access_token'], (int)$response['expires_in']);

                $accessToken = (string)$response['access_token'];
            }
        } catch (GuzzleException $exception) {
            throw new AuthenticationException('Access Token could not be retrieved from Twitch.');
        }

        return $accessToken;
    }
}
