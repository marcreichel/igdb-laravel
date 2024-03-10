<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Tests;

use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use JsonException;
use MarcReichel\IGDBLaravel\Builder;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use MarcReichel\IGDBLaravel\Exceptions\MissingEndpointException;
use MarcReichel\IGDBLaravel\Exceptions\ModelNotFoundException;
use ReflectionException;
use stdClass;

/**
 * @internal
 */
class BuilderTest extends TestCase
{
    private Builder $igdb;

    public function setUp(): void
    {
        parent::setUp();

        Cache::put('igdb_cache.access_token', 'some-token');

        Http::fake([
            '*/oauth2/token*' => Http::response([
                'access_token' => 'test-suite-token',
                'expires_in' => 3600,
            ]),
            '*/games/webhooks' => fn (Request $request) => $this->createWebhookResponse($request),
            '*/companies/webhooks' => fn (Request $request) => $this->createWebhookResponse($request),
            '*/artworks/webhooks' => fn (Request $request) => $this->createWebhookResponse($request),
            '*/webhooks' => Http::response(),
            '*/count' => Http::response(['count' => 1337]),
            '*/companies' => Http::response(['id' => 1337, 'name' => 'Fortnite']),
            '*' => Http::response(),
        ]);

        $this->igdb = new Builder('games');
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldGenerateFieldsQuery(): void
    {
        $this->igdb->select(['name'])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'fields name;'));
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldGenerateAsteriskFieldsQueryWithEmptyFields(): void
    {
        $this->igdb->select([])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'fields *;'));
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldFilterExpendedFieldQuery(): void
    {
        $this->igdb->select(['company.logo.*'])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'fields *;'));
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldGenerateSearchQuery(): void
    {
        $this->igdb->search('Fortnite')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'search "Fortnite";'));
    }

    /**
     * @throws MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateFuzzySearchQuery(): void
    {
        $this->igdb->fuzzySearch(['name', 'company'], 'phpunit test')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall(
                $request,
                'games',
                'where (name ~ *"phpunit"* | name ~ *"test"* | company ~ *"phpunit"* | company ~ *"test"*);',
            );
        });
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateOrFuzzySearchQuery(): void
    {
        $this->igdb->where('name', 'Fortnite')->orFuzzySearch(['name', 'company'], 'phpunit test')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall(
                $request,
                'games',
                'where name = "Fortnite" | (name ~ *"phpunit"* | name ~ *"test"* | company ~ *"phpunit"* | company ~ *"test"*);',
            );
        });
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereEqualsQueryWithOperator(): void
    {
        $this->igdb->where('name', '=', 'Fortnite')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite";'));

        $timestamp = Carbon::now()->timestamp;

        $this->igdb->where('first_release_date', '>=', $timestamp)->get();

        Http::assertSent(
            fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date >= $timestamp;"),
        );
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateNestedWhereQuery(): void
    {
        $now = Carbon::now()->timestamp;
        $nextYear = Carbon::now()->addYear()->timestamp;
        $this->igdb->where(function ($query) use ($now, $nextYear): void {
            $query->where('first_release_date', '>=', $now)
                ->where('first_release_date', '<=', $nextYear);
        })->orWhere('name', 'Fortnite')->get();

        Http::assertSent(function (Request $request) use ($now, $nextYear) {
            return $this->isApiCall(
                $request,
                'games',
                "where (first_release_date >= $now & first_release_date <= $nextYear) | name = \"Fortnite\";",
            );
        });
    }

    /**
     * @throws MissingEndpointException
     * @throws ReflectionException
     * @throws InvalidParamsException
     * @throws JsonException
     */
    public function testItShouldThrowExceptionForInvalidWhereKey(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->igdb->where(123, 'Fortnite')->get();

        Http::assertNotSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where 123 = "Fortnite";'));
    }

    /**
     * @throws MissingEndpointException
     * @throws ReflectionException
     * @throws InvalidParamsException
     * @throws JsonException
     */
    public function testItShouldThrowExceptionForInvalidOperator(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->igdb->where('name', 123, 'Fortnite')->get();

        Http::assertNotSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name 123 "Fortnite";'));
    }

    /**
     * @throws ReflectionException
     * @throws InvalidParamsException
     */
    public function testItShouldThrowExceptionForInvalidModel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Builder(new stdClass());
    }

    public static function datesDataProvider(): array
    {
        return [
            ['created_at'],
            ['updated_at'],
            ['change_date'],
            ['start_date'],
            ['published_at'],
            ['first_release_date'],
        ];
    }

    /**
     * @dataProvider datesDataProvider
     *
     * @throws ReflectionException
     * @throws InvalidParamsException
     * @throws JsonException
     * @throws MissingEndpointException
     */
    public function testItShouldCastDateStrings(string $key): void
    {
        $dateString = '2024-01-01';
        $timestamp = Carbon::parse($dateString)->timestamp;

        $this->igdb->where($key, '2024-01-01')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where ' . $key . ' = ' . $timestamp . ';'));
    }

    /**
     * @dataProvider datesDataProvider
     *
     * @throws MissingEndpointException
     * @throws ReflectionException
     * @throws InvalidParamsException
     * @throws JsonException
     */
    public function testItShouldCastCarbonInstances(string $key): void
    {
        $now = Carbon::now();
        $timestamp = $now->timestamp;

        $this->igdb->where($key, $now)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where ' . $key . ' = ' . $timestamp . ';'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereEqualsQueryWithoutOperator(): void
    {
        $this->igdb->where('name', 'Fortnite')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite";'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldFillUpFieldsFromWhereClause(): void
    {
        $this->igdb->select('company')->where('name', 'Fortnite')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "fields company,name;\nwhere name = \"Fortnite\";"));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateAndWhereQuery(): void
    {
        $this->igdb->where('name', 'Fortnite')->where('name', 'Borderlands 2')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" & name = "Borderlands 2";'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateOrWhereQuery(): void
    {
        $this->igdb->where('name', 'Fortnite')->orWhere('name', 'Borderlands 2')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | name = "Borderlands 2";'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereLikeQueriesViaNormalWhereMethod(): void
    {
        $this->igdb->where('name', 'like', '%Fort%')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = *"Fort"*;'));

        $this->igdb->where('name', 'ilike', '%Fort%')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name ~ *"Fort"*;'));

        $this->igdb->where('name', 'not like', '%Fort%')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name != *"Fort"*;'));

        $this->igdb->where('name', 'not ilike', '%Fort%')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name !~ *"Fort"*;'));
    }

    /**
     * @throws JsonException|MissingEndpointException
     */
    public function testItShouldGenerateWhereLikeQueriesViaWhereLikeMethod(): void
    {
        $this->igdb->whereLike('name', '%Fort%')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = *"Fort"*;'));

        $this->igdb->whereLike('name', '%Fort%', false)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name ~ *"Fort"*;'));

        $this->igdb->whereNotLike('name', '%Fort%')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name != *"Fort"*;'));

        $this->igdb->whereNotLike('name', '%Fort%', false)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name !~ *"Fort"*;'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateOrWhereLikeQueriesViaOrWhereLikeMethods(): void
    {
        $this->igdb->where('name', 'Fortnite')->orWhereLike('name', '%Fort%')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | name = *"Fort"*;'));

        $this->igdb->where('name', 'Fortnite')->orWhereLike('name', '%Fort%', false)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | name ~ *"Fort"*;'));

        $this->igdb->where('name', 'Fortnite')->orWhereNotLike('name', '%Fort%')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | name != *"Fort"*;'));

        $this->igdb->where('name', 'Fortnite')->orWhereNotLike('name', '%Fort%', false)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | name !~ *"Fort"*;'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldThrowExceptionWithInvalidOperatorAndValueCombination(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->igdb->where('name', 'like', null)->get();
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereBetweenQuery(): void
    {
        $this->igdb->whereBetween('first_release_date', 1546297200, 1577833199)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall(
                $request,
                'games',
                'where (first_release_date >= 1546297200 & first_release_date <= 1577833199);',
            );
        });

        $this->igdb->where('name', 'Fortnite')->orWhereBetween('first_release_date', 1546297200, 1577833199)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall(
                $request,
                'games',
                'where name = "Fortnite" | (first_release_date >= 1546297200 & first_release_date <= 1577833199);',
            );
        });

        $this->igdb->whereNotBetween('first_release_date', 1546297200, 1577833199)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall(
                $request,
                'games',
                'where (first_release_date < 1546297200 | first_release_date > 1577833199);',
            );
        });

        $this->igdb->where('name', 'Fortnite')->orWhereNotBetween('first_release_date', 1546297200, 1577833199)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall(
                $request,
                'games',
                'where name = "Fortnite" | (first_release_date < 1546297200 | first_release_date > 1577833199);',
            );
        });
    }

    /**
     * @throws MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereBetweenQueryWithoutBoundaries(): void
    {
        $this->igdb->whereBetween('first_release_date', 1546297200, 1577833199, false)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall(
                $request,
                'games',
                'where (first_release_date > 1546297200 & first_release_date < 1577833199);',
            );
        });
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereInQuery(): void
    {
        $this->igdb->whereIn('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category = (0,4);'));

        $this->igdb->where('name', 'Fortnite')->orWhereIn('name', ['Borderlands', 'Call of Duty'])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | name = ("Borderlands","Call of Duty");'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereInExactQuery(): void
    {
        $this->igdb->whereInExact('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category = {0,4};'));

        $this->igdb->where('name', 'Fortnite')->orWhereInExact('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | category = {0,4};'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereInAllQuery(): void
    {
        $this->igdb->whereInAll('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category = [0,4];'));

        $this->igdb->where('name', 'Fortnite')->orWhereInAll('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | category = [0,4];'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereNotInQuery(): void
    {
        $this->igdb->whereNotIn('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category != (0,4);'));

        $this->igdb->where('name', 'Fortnite')->orWhereNotIn('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | category != (0,4);'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereNotInExactQuery(): void
    {
        $this->igdb->whereNotInExact('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category != {0,4};'));

        $this->igdb->where('name', 'Fortnite')->orWhereNotInExact('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | category != {0,4};'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereNotInAllQuery(): void
    {
        $this->igdb->whereNotInAll('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category != [0,4];'));

        $this->igdb->where('name', 'Fortnite')->orWhereNotInAll('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | category != [0,4];'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereNullQuery(): void
    {
        $this->igdb->whereNull('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where first_release_date = null;'));

        $this->igdb->where('name', 'Fortnite')->orWhereNull('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | first_release_date = null;'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereNotNullQuery(): void
    {
        $this->igdb->whereNotNull('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where first_release_date != null;'));

        $this->igdb->where('name', 'Fortnite')->orWhereNotNull('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | first_release_date != null;'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereDateEqualsQuery(): void
    {
        $date = now();
        $start = $date->clone()->startOfDay()->timestamp;
        $end = $date->clone()->endOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall(
                $request,
                'games',
                "where (first_release_date >= $start & first_release_date <= $end);",
            );
        });

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall(
                $request,
                'games',
                "where name = \"Fortnite\" | (first_release_date >= $start & first_release_date <= $end);",
            );
        });
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereDateLargerOrEqualsQuery(): void
    {
        $date = now();
        $start = $date->clone()->startOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '>=', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date >= $start;"));

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', '>=', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where name = \"Fortnite\" | first_release_date >= $start;"));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereDateLargerQuery(): void
    {
        $date = now();
        $end = $date->clone()->addDay()->startOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '>', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date > $end;"));

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', '>', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where name = \"Fortnite\" | first_release_date > $end;"));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereDateSmallerOrEqualsQuery(): void
    {
        $date = now();
        $end = $date->clone()->endOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '<=', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date <= $end;"));

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', '<=', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where name = \"Fortnite\" | first_release_date <= $end;"));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereDateSmallerQuery(): void
    {
        $date = now();
        $start = $date->clone()->subDay()->endOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '<', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date < $start;"));

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', '<', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where name = \"Fortnite\" | first_release_date < $start;"));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereDateNotEqualsQuery(): void
    {
        $date = now();
        $start = $date->clone()->startOfDay()->timestamp;
        $end = $date->clone()->endOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '!=', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where (first_release_date < $start | first_release_date > $end);"));

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', '!=', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where name = \"Fortnite\" | (first_release_date < $start | first_release_date > $end);"));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereYearEqualsQuery(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;
        $end = $date->clone()->endOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall(
                $request,
                'games',
                "where (first_release_date >= $start & first_release_date <= $end);",
            );
        });

        $this->igdb->where('name', 'Fortnite')->orWhereYear('first_release_date', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall(
                $request,
                'games',
                "where name = \"Fortnite\" | (first_release_date >= $start & first_release_date <= $end);",
            );
        });
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereYearLargerOrEqualsQuery(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '>=', $date->year)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date >= $start;"));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereYearLargerQuery(): void
    {
        $date = now();
        $end = $date->clone()->endOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '>', $date->year)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date > $end;"));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereYearSmallerOrEqualsQuery(): void
    {
        $date = now();
        $end = $date->clone()->endOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '<=', $date->year)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date <= $end;"));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereYearSmallerQuery(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '<', $date->year)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date < $start;"));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereHasQuery(): void
    {
        $this->igdb->whereHas('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where first_release_date != null;'));

        $this->igdb->where('name', 'Fortnite')->orWhereHas('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | first_release_date != null;'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateWhereHasNotQuery(): void
    {
        $this->igdb->whereHasNot('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where first_release_date = null;'));

        $this->igdb->where('name', 'Fortnite')->orWhereHasNot('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | first_release_date = null;'));
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateNestedOrWhereQuery(): void
    {
        $this->igdb->where('name', 'Fortnite')
            ->orWhere(function ($query): void {
                $query->where('aggregated_rating', '>=', 90)
                    ->where('aggregated_rating_count', '>=', 3000);
            })->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall(
                $request,
                'games',
                'where name = "Fortnite" | (aggregated_rating >= 90 & aggregated_rating_count >= 3000);',
            );
        });
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateArrayWhereQuery(): void
    {
        $this->igdb->where([['name', 'Fortnite'], ['name', 'Borderlands 2']])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where (name = "Fortnite" & name = "Borderlands 2");'));

        $this->igdb->where('name', 'Fortnite')->orWhere([['name', 'Call of Duty'], ['name', 'Borderlands 2']])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall(
                $request,
                'games',
                'where name = "Fortnite" | (name = "Call of Duty" & name = "Borderlands 2");',
            );
        });

        $now = Carbon::now()->timestamp;

        $this->igdb->where('name', 'Fortnite')->where(['name' => 'Borderlands', 'first_release_date' => $now])->get();

        Http::assertSent(function (Request $request) use ($now) {
            return $this->isApiCall(
                $request,
                'games',
                'where name = "Fortnite" & (name = "Borderlands" & first_release_date = ' . $now . ');',
            );
        });
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldThrowExceptionWithInvalidPrefixAndSuffixInWhereInQuery(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->igdb->whereIn('name', ['Borderlands', 'Fortnite'], '&', '=', '(', '}')->get();
    }

    /**
     * @throws InvalidParamsException
     * @throws MissingEndpointException
     */
    public function testItShouldGenerateOrderByQuery(): void
    {
        $this->igdb->orderBy('name')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'sort name asc;'));

        $this->igdb->orderBy('name', 'desc')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'sort name desc;'));

        $this->igdb->orderBy('name')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'sort name asc;'));
    }

    /**
     * @throws InvalidParamsException
     * @throws MissingEndpointException
     */
    public function testItShouldNotGenerateOrderByQueryWithInvalidParams(): void
    {
        $this->expectException(InvalidParamsException::class);

        $this->igdb->orderBy('name', 'foo')->get();

        Http::assertNotSent(fn (Request $request) => $this->isApiCall($request, 'games', 'sort name foo;'));
    }

    /**
     * @throws InvalidParamsException
     * @throws MissingEndpointException
     */
    public function testItShouldGenerateOrderByDescQuery(): void
    {
        $this->igdb->orderByDesc('name')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'sort name desc;'));
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldGenerateOffsetQuery(): void
    {
        $this->igdb->skip(10)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'offset 10;'));

        $this->igdb->offset(10)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'offset 10;'));
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldGenerateLimitQuery(): void
    {
        $this->igdb->take(10)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'limit 10;'));

        $this->igdb->limit(10)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'limit 10;'));
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldGenerateForPageQuery(): void
    {
        $this->igdb->forPage(2, 20)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 20;\noffset 20;"));
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldGenerateAllQuery(): void
    {
        $this->igdb->all();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 500;\noffset 0;"));
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldGenerateFirstQuery(): void
    {
        $this->igdb->first();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 1;\noffset 0;"));
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldThrowExceptionForFirstOrFailMethod(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->igdb->firstOrFail();
    }

    /**
     * @throws InvalidParamsException
     * @throws JsonException
     * @throws MissingEndpointException
     * @throws ModelNotFoundException
     * @throws ReflectionException
     */
    public function testItShouldThrowExceptionForFindOrFailMethod(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->igdb->findOrFail(1337);
    }

    /**
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function testItShouldGenerateFindQuery(): void
    {
        $this->igdb->find(1905);

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 1;\noffset 0;\nwhere id = 1905;"));
    }

    public function testItShouldThrowExceptionForInvalidCacheLifetime(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Config::set('igdb.cache_lifetime', 'foo');

        new Builder('games');
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldGeneratePaginateQuery(): void
    {
        $this->igdb->paginate(20);

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 20;\noffset 0;"));

        $this->get('/?page=2');

        $this->igdb->paginate(20);

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 20;\noffset 20;"));
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldGenerateExpandingQuery(): void
    {
        $this->igdb->with(['cover', 'artworks'])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'fields *,cover.*,artworks.*;'));

        $this->igdb->with(['involved_companies', 'involved_companies.company'])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'fields *,involved_companies.*,involved_companies.company.*;'));

        $this->igdb->with(['cover' => ['url'], 'artworks' => ['url']])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'fields *,cover.url,artworks.url;'));

        $this->igdb->with(['artworks' => [], 'cover' => []])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'fields *,artworks.*,cover.*;'));
    }

    /**
     * @throws InvalidParamsException|JsonException|MissingEndpointException|ReflectionException
     */
    public function testItShouldOverwriteCache(): void
    {
        $builder = $this->igdb->where('name', 'Fortnite Unit Test');

        $query = $builder->getQuery();

        $builder->cache(0)->get();

        $this->assertFalse(Cache::has('igdb_cache.' . md5('games' . $query)));
    }

    /**
     * @throws InvalidParamsException|JsonException|MissingEndpointException|ReflectionException
     */
    public function testItShouldSetEndpointLater(): void
    {
        $igdb = new Builder();
        $igdb->endpoint('artworks')->where('url', 'test')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'artworks', 'where url = "test";'));
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldRequestCountEndpoint(): void
    {
        $count = $this->igdb->count();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games/count', 'fields *;'));

        self::assertEquals(1337, $count);
    }

    /**
     * @throws MissingEndpointException
     */
    public function testItShouldThrowExceptionWhenNoEndpointIsSet(): void
    {
        $this->expectException(MissingEndpointException::class);

        (new Builder())->get();
    }
}
