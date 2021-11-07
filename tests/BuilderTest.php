<?php

namespace MarcReichel\IGDBLaravel\Tests;

use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use JsonException;
use MarcReichel\IGDBLaravel\Builder;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use MarcReichel\IGDBLaravel\Exceptions\MissingEndpointException;
use MarcReichel\IGDBLaravel\Exceptions\ModelNotFoundException;
use ReflectionException;

class BuilderTest extends TestCase
{
    /**
     * @var Builder $igdb
     */
    private Builder $igdb;

    public function setUp(): void
    {
        parent::setUp();

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

        $this->igdb = new Builder('games');
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_generate_fields_query(): void
    {
        $this->igdb->select(['name'])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'fields name;');
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_generate_asterisk_fields_query_with_empty_fields(): void
    {
        $this->igdb->select([])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'fields *;');
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_filter_expended_field_query(): void
    {
        $this->igdb->select(['company.logo.*'])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'fields *;');
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_generate_search_query(): void
    {
        $this->igdb->search('Fortnite')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'search "Fortnite";');
        });
    }

    /**
     * @test
     * @throws MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_fuzzy_search_query(): void
    {
        $this->igdb->fuzzySearch(['name', 'company'], 'phpunit test')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games',
                'where (name ~ *"phpunit"* | name ~ *"test"* | company ~ *"phpunit"* | company ~ *"test"*);');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_or_fuzzy_search_query(): void
    {
        $this->igdb->where('name', 'Fortnite')->orFuzzySearch(['name', 'company'], 'phpunit test')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games',
                'where name = "Fortnite" | (name ~ *"phpunit"* | name ~ *"test"* | company ~ *"phpunit"* | company ~ *"test"*);');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_equals_query_with_operator(): void
    {
        $this->igdb->where('name', '=', 'Fortnite')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite";');
        });

        $timestamp = Carbon::now()->timestamp;

        $this->igdb->where('first_release_date', '>=', $timestamp)->get();

        Http::assertSent(function (Request $request) use ($timestamp) {
            return $this->isApiCall($request, 'games', "where first_release_date >= $timestamp;");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_nested_where_query(): void
    {
        $now = Carbon::now()->timestamp;
        $nextYear = Carbon::now()->addYear()->timestamp;
        $this->igdb->where(function ($query) use ($now, $nextYear) {
            $query->where('first_release_date', '>=', $now)
                ->where('first_release_date', '<=', $nextYear);
        })->orWhere('name', 'Fortnite')->get();

        Http::assertSent(function (Request $request) use ($now, $nextYear) {
            return $this->isApiCall($request, 'games',
                "where (first_release_date >= $now & first_release_date <= $nextYear) | name = \"Fortnite\";");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_equals_query_without_operator(): void
    {
        $this->igdb->where('name', 'Fortnite')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite";');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_fill_up_fields_from_where_clause(): void
    {
        $this->igdb->select('company')->where('name', 'Fortnite')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "fields company,name;\nwhere name = \"Fortnite\";");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_and_where_query(): void
    {
        $this->igdb->where('name', 'Fortnite')->where('name', 'Borderlands 2')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" & name = "Borderlands 2";');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_or_where_query(): void
    {
        $this->igdb->where('name', 'Fortnite')->orWhere('name', 'Borderlands 2')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | name = "Borderlands 2";');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_like_queries_via_normal_where_method(): void
    {
        $this->igdb->where('name', 'like', '%Fort%')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = *"Fort"*;');
        });

        $this->igdb->where('name', 'ilike', '%Fort%')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name ~ *"Fort"*;');
        });

        $this->igdb->where('name', 'not like', '%Fort%')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name != *"Fort"*;');
        });

        $this->igdb->where('name', 'not ilike', '%Fort%')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name !~ *"Fort"*;');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException
     */
    public function it_should_generate_where_like_queries_via_where_like_method(): void
    {
        $this->igdb->whereLike('name', '%Fort%')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = *"Fort"*;');
        });

        $this->igdb->whereLike('name', '%Fort%', false)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name ~ *"Fort"*;');
        });

        $this->igdb->whereNotLike('name', '%Fort%')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name != *"Fort"*;');
        });

        $this->igdb->whereNotLike('name', '%Fort%', false)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name !~ *"Fort"*;');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_or_where_like_queries_via_or_where_like_methods(): void
    {
        $this->igdb->where('name', 'Fortnite')->orWhereLike('name', '%Fort%')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | name = *"Fort"*;');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereLike('name', '%Fort%', false)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | name ~ *"Fort"*;');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereNotLike('name', '%Fort%')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | name != *"Fort"*;');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereNotLike('name', '%Fort%', false)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | name !~ *"Fort"*;');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_throw_exception_with_invalid_operator_and_value_combination(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->igdb->where('name', 'like', null)->get();
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_between_query(): void
    {
        $this->igdb->whereBetween('first_release_date', 1546297200, 1577833199)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games',
                'where (first_release_date >= 1546297200 & first_release_date <= 1577833199);');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereBetween('first_release_date', 1546297200, 1577833199)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games',
                'where name = "Fortnite" | (first_release_date >= 1546297200 & first_release_date <= 1577833199);');
        });

        $this->igdb->whereNotBetween('first_release_date', 1546297200, 1577833199)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games',
                'where (first_release_date < 1546297200 | first_release_date > 1577833199);');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereNotBetween('first_release_date', 1546297200, 1577833199)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games',
                'where name = "Fortnite" | (first_release_date < 1546297200 | first_release_date > 1577833199);');
        });
    }

    /**
     * @test
     * @throws MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_between_query_without_boundaries(): void
    {
        $this->igdb->whereBetween('first_release_date', 1546297200, 1577833199, false)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games',
                'where (first_release_date > 1546297200 & first_release_date < 1577833199);');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_in_query(): void
    {
        $this->igdb->whereIn('category', [0, 4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category = (0,4);');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereIn('name', ['Borderlands', 'Call of Duty'])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | name = ("Borderlands","Call of Duty");');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_in_exact_query(): void
    {
        $this->igdb->whereInExact('category', [0, 4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category = {0,4};');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereInExact('category', [0, 4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | category = {0,4};');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_in_all_query(): void
    {
        $this->igdb->whereInAll('category', [0, 4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category = [0,4];');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereInAll('category', [0, 4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | category = [0,4];');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_not_in_query(): void
    {
        $this->igdb->whereNotIn('category', [0, 4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category != (0,4);');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereNotIn('category', [0, 4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | category != (0,4);');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_not_in_exact_query(): void
    {
        $this->igdb->whereNotInExact('category', [0, 4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category != {0,4};');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereNotInExact('category', [0, 4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | category != {0,4};');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_not_in_all_query(): void
    {
        $this->igdb->whereNotInAll('category', [0, 4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category != [0,4];');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereNotInAll('category', [0, 4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | category != [0,4];');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_null_query(): void
    {
        $this->igdb->whereNull('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date = null;');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereNull('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | first_release_date = null;');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_not_null_query(): void
    {
        $this->igdb->whereNotNull('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date != null;');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereNotNull('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | first_release_date != null;');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_date_equals_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfDay()->timestamp;
        $end = $date->clone()->endOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall($request, 'games',
                "where (first_release_date >= $start & first_release_date <= $end);");
        });

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall($request, 'games',
                "where name = \"Fortnite\" | (first_release_date >= $start & first_release_date <= $end);");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_date_larger_or_equals_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '>=', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where first_release_date >= $start;");
        });

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', '>=', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where name = \"Fortnite\" | first_release_date >= $start;");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_date_larger_query(): void
    {
        $date = now();
        $end = $date->clone()->addDay()->startOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '>', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date > $end;");
        });

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', '>', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where name = \"Fortnite\" | first_release_date > $end;");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_date_smaller_or_equals_query(): void
    {
        $date = now();
        $end = $date->clone()->endOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '<=', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date <= $end;");
        });

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', '<=', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where name = \"Fortnite\" | first_release_date <= $end;");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_date_smaller_query(): void
    {
        $date = now();
        $start = $date->clone()->subDay()->endOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '<', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where first_release_date < $start;");
        });

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', '<', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where name = \"Fortnite\" | first_release_date < $start;");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_date_not_equals_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfDay()->timestamp;
        $end = $date->clone()->endOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '!=', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall($request, 'games', "where (first_release_date < $start | first_release_date > $end);");
        });

        $this->igdb->where('name', 'Fortnite')->orWhereDate('first_release_date', '!=', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall($request, 'games', "where name = \"Fortnite\" | (first_release_date < $start | first_release_date > $end);");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_year_equals_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;
        $end = $date->clone()->endOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall($request, 'games',
                "where (first_release_date >= $start & first_release_date <= $end);");
        });

        $this->igdb->where('name', 'Fortnite')->orWhereYear('first_release_date', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall($request, 'games',
                "where name = \"Fortnite\" | (first_release_date >= $start & first_release_date <= $end);");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_year_larger_or_equals_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '>=', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where first_release_date >= $start;");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_year_larger_query(): void
    {
        $date = now();
        $end = $date->clone()->endOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '>', $date->year)->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date > $end;");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_year_smaller_or_equals_query(): void
    {
        $date = now();
        $end = $date->clone()->endOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '<=', $date->year)->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date <= $end;");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_year_smaller_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '<', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where first_release_date < $start;");
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_has_query(): void
    {
        $this->igdb->whereHas('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date != null;');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereHas('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | first_release_date != null;');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_where_has_not_query(): void
    {
        $this->igdb->whereHasNot('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date = null;');
        });

        $this->igdb->where('name', 'Fortnite')->orWhereHasNot('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | first_release_date = null;');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_nested_or_where_query(): void
    {
        $this->igdb->where('name', 'Fortnite')
            ->orWhere(function ($query) {
                $query->where('aggregated_rating', '>=', 90)
                    ->where('aggregated_rating_count', '>=', 3000);
            })->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games',
                'where name = "Fortnite" | (aggregated_rating >= 90 & aggregated_rating_count >= 3000);');
        });
    }

    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_array_where_query(): void
    {
        $this->igdb->where([['name', 'Fortnite'], ['name', 'Borderlands 2']])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where (name = "Fortnite" & name = "Borderlands 2");');
        });

        $this->igdb->where('name', 'Fortnite')->orWhere([['name', 'Call of Duty'], ['name', 'Borderlands 2']])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games',
                'where name = "Fortnite" | (name = "Call of Duty" & name = "Borderlands 2");');
        });

        $now = Carbon::now()->timestamp;

        $this->igdb->where('name', 'Fortnite')->where(['name' => 'Borderlands', 'first_release_date' => $now])->get();

        Http::assertSent(function (Request $request) use ($now) {
            return $this->isApiCall($request, 'games',
                'where name = "Fortnite" & (name = "Borderlands" & first_release_date = ' . $now . ');');
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_throw_exception_with_invalid_prefix_and_suffix_in_where_in_query(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->igdb->whereIn('name', ['Borderlands', 'Fortnite'], '&', '=', '(', '}')->get();
    }

    /**
     * @test
     * @throws InvalidParamsException
     * @throws MissingEndpointException
     */
    public function it_should_generate_orderby_query(): void
    {
        $this->igdb->orderBy('name')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name asc;');
        });

        $this->igdb->orderBy('name', 'desc')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name desc;');
        });

        $this->igdb->orderBy('name')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name asc;');
        });
    }

    /**
     * @test
     * @throws InvalidParamsException
     * @throws MissingEndpointException
     */
    public function it_should_not_generate_orderby_query_with_invalid_params(): void
    {
        $this->expectException(InvalidParamsException::class);

        $this->igdb->orderBy('name', 'foo')->get();

        Http::assertNotSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name foo;');
        });
    }

    /**
     * @test
     * @throws InvalidParamsException
     * @throws MissingEndpointException
     */
    public function it_should_generate_orderbydesc_query(): void
    {
        $this->igdb->orderByDesc('name')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name desc;');
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_generate_offset_query(): void
    {
        $this->igdb->skip(10)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'offset 10;');
        });

        $this->igdb->offset(10)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'offset 10;');
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_generate_limit_query(): void
    {
        $this->igdb->take(10)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'limit 10;');
        });

        $this->igdb->limit(10)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'limit 10;');
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_generate_forPage_query(): void
    {
        $this->igdb->forPage(2, 20)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 20;\noffset 20;");
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_generate_all_query(): void
    {
        $this->igdb->all();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 500;\noffset 0;");
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_generate_first_query(): void
    {
        $this->igdb->first();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 1;\noffset 0;");
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_throw_exception_for_first_or_fail_method(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->igdb->firstOrFail();
    }

    /**
     * @test
     * @throws InvalidParamsException
     * @throws JsonException
     * @throws MissingEndpointException
     * @throws ModelNotFoundException
     * @throws ReflectionException
     */
    public function it_should_throw_exception_for_find_or_fail_method(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->igdb->findOrFail(1337);
    }


    /**
     * @test
     * @throws JsonException|MissingEndpointException|ReflectionException|InvalidParamsException
     */
    public function it_should_generate_find_query(): void
    {
        $this->igdb->find(1905);

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 1;\noffset 0;\nwhere id = 1905;");
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_generate_paginate_query(): void
    {
        $this->igdb->paginate(20);

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 20;\noffset 0;");
        });

        $this->get('/?page=2');

        $this->igdb->paginate(20);

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 20;\noffset 20;");
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_generate_expanding_query(): void
    {
        $this->igdb->with(['cover', 'artworks'])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "fields *,cover.*,artworks.*;");
        });

        $this->igdb->with(['involved_companies', 'involved_companies.company'])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "fields *,involved_companies.*,involved_companies.company.*;");
        });

        $this->igdb->with(['cover' => ['url'], 'artworks' => ['url']])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "fields *,cover.url,artworks.url;");
        });

        $this->igdb->with(['artworks' => [], 'cover' => []])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "fields *,artworks.*,cover.*;");
        });
    }

    /**
     * @test
     * @throws InvalidParamsException|JsonException|MissingEndpointException|ReflectionException
     */
    public function it_should_overwrite_cache(): void
    {
        $builder = $this->igdb->where('name', 'Fortnite Unit Test');

        $query = $builder->getQuery();

        $builder->cache(0)->get();

        $this->assertFalse(Cache::has('igdb_cache.' . md5('games' . $query)));
    }

    /**
     * @test
     * @throws InvalidParamsException|JsonException|MissingEndpointException|ReflectionException
     */
    public function it_should_set_endpoint_later(): void
    {
        $igdb = new Builder();
        $igdb->endpoint('artworks')->where('url', 'test')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'artworks', "where url = \"test\";");
        });
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_request_count_endpoint(): void
    {
        $count = $this->igdb->count();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games/count', 'fields *;');
        });

        self::assertEquals(1337, $count);
    }

    /**
     * @test
     * @throws MissingEndpointException
     */
    public function it_should_throw_exception_when_no_endpoint_is_set(): void
    {
        $this->expectException(MissingEndpointException::class);

        $igdb = new Builder();

        $igdb->get();
    }
}
