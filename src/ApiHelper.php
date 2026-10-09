<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;
use Throwable;

class ApiHelper
{
    public const IGDB_BASE_URI = 'https://api.igdb.com/v4/';

    private const ACCESS_TOKEN_CACHE_KEY = 'igdb_cache.access_token';

    /**
     * Builds an IGDB client which retries failed requests and refreshes the
     * access token if it got rejected.
     *
     * @throws AuthenticationException
     */
    public static function client(?int $retries = null): PendingRequest
    {
        $token = self::retrieveAccessToken();

        return Http::baseUrl(self::IGDB_BASE_URI)
            ->acceptJson()
            ->withHeaders(['Client-ID' => config('igdb.credentials.client_id')])
            ->withToken($token)
            ->retry($retries ?? (int) config('igdb.retries'), 100, static function (Throwable $exception, PendingRequest $request) use (&$token): bool {
                if ($exception instanceof RequestException && $exception->response->unauthorized()) {
                    // Another request may already have refreshed the token.
                    if (Cache::get(self::ACCESS_TOKEN_CACHE_KEY) === $token) {
                        Cache::forget(self::ACCESS_TOKEN_CACHE_KEY);
                    }
                    $token = self::retrieveAccessToken();
                    $request->withToken($token);
                }

                return true;
            }, false);
    }

    /**
     * Retrieves an Access Token from Twitch.
     *
     * @throws AuthenticationException
     */
    public static function retrieveAccessToken(): string
    {
        $accessToken = Cache::get(self::ACCESS_TOKEN_CACHE_KEY, '');

        if ($accessToken) {
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
                Cache::put(self::ACCESS_TOKEN_CACHE_KEY, (string) $response['access_token'], (int) $response['expires_in'] - 60);

                $accessToken = $response['access_token'];
            }
        } catch (Exception) {
            throw new AuthenticationException('Access Token could not be retrieved from Twitch.');
        }

        return (string) $accessToken;
    }
}
