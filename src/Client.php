<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use MarcReichel\IGDBLaravel\Exceptions\AuthenticationException;

/**
 * @internal
 */
final class Client
{
    public static function get(string $endpoint, string $query, int $cacheLifetime, ?int $retries = null): mixed
    {
        $cacheKey = self::handleCache($endpoint, $query, $cacheLifetime);

        return Cache::remember($cacheKey, $cacheLifetime, static fn () => self::request($endpoint, $query, $retries));
    }

    public static function count(string $endpoint, string $query, int $cacheLifetime, ?int $retries = null): int
    {
        $endpoint = Str::finish($endpoint, '/count');
        $cacheKey = self::handleCache($endpoint, $query, $cacheLifetime);

        return Cache::remember($cacheKey, $cacheLifetime, static function () use ($endpoint, $query, $retries): int {
            $response = self::request($endpoint, $query, $retries);
            if (is_array($response)) {
                return (int) $response['count'];
            }

            return 0;
        });
    }

    private static function handleCache(string $endpoint, string $query, int $cacheLifetime): string
    {
        $key = config('igdb.cache_prefix', 'igdb_cache') . '.' . md5($endpoint . $query);

        if ($cacheLifetime === 0) {
            Cache::forget($key);
        }

        return $key;
    }

    /**
     * @throws AuthenticationException
     * @throws RequestException
     */
    private static function request(string $endpoint, string $query, ?int $retries): mixed
    {
        return ApiHelper::client($retries)
            ->withBody($query, 'plain/text')
            ->post($endpoint)
            ->throw()
            ->json();
    }
}
