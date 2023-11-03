<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Tests;

use BadMethodCallException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use MarcReichel\IGDBLaravel\Exceptions\ModelNotFoundException;
use MarcReichel\IGDBLaravel\Models\Game;

/**
 * @internal
 */
class ModelTest extends TestCase
{
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
    }

    public function testItShouldGenerateFieldsQuery(): void
    {
        Game::select(['name'])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'fields name;'));
    }

    public function testItShouldGenerateSearchQuery(): void
    {
        Game::search('Fortnite')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'search "Fortnite";'));
    }

    public function testItShouldGenerateWhereEqualsQueryWithOperator(): void
    {
        Game::where('name', '=', 'Fortnite')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite";'));

        Game::where('first_release_date', '>=', 1546297200)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where first_release_date >= 1546297200;'));
    }

    public function testItShouldGenerateWhereEqualsQueryWithoutOperator(): void
    {
        Game::where('name', 'Fortnite')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite";'));
    }

    public function testItShouldGenerateAndWhereQuery(): void
    {
        Game::where('name', 'Fortnite')->where('name', 'Borderlands 2')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" & name = "Borderlands 2";'));
    }

    public function testItShouldGenerateOrWhereQuery(): void
    {
        Game::where('name', 'Fortnite')->orWhere('name', 'Borderlands 2')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | name = "Borderlands 2";'));
    }

    public function testItShouldGenerateWhereBetweenQuery(): void
    {
        Game::whereBetween('first_release_date', 1546297200, 1577833199)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where (first_release_date >= 1546297200 & first_release_date <= 1577833199);'));
    }

    public function testItShouldGenerateWhereBetweenQueryWithoutBoundaries(): void
    {
        Game::whereBetween('first_release_date', 1546297200, 1577833199, false)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where (first_release_date > 1546297200 & first_release_date < 1577833199);'));
    }

    public function testItShouldGenerateWhereInQuery(): void
    {
        Game::whereIn('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category = (0,4);'));
    }

    public function testItShouldGenerateWhereInExactQuery(): void
    {
        Game::whereInExact('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category = {0,4};'));
    }

    public function testItShouldGenerateWhereInAllQuery(): void
    {
        Game::whereInAll('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category = [0,4];'));
    }

    public function testItShouldGenerateWhereNotInQuery(): void
    {
        Game::whereNotIn('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category != (0,4);'));
    }

    public function testItShouldGenerateWhereNotInExactQuery(): void
    {
        Game::whereNotInExact('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category != {0,4};'));
    }

    public function testItShouldGenerateWhereNotInAllQuery(): void
    {
        Game::whereNotInAll('category', [0, 4])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where category != [0,4];'));
    }

    public function testItShouldGenerateWhereNullQuery(): void
    {
        Game::whereNull('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where first_release_date = null;'));
    }

    public function testItShouldGenerateWhereNotNullQuery(): void
    {
        Game::whereNotNull('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where first_release_date != null;'));
    }

    public function testItShouldGenerateWhereDateEqualsQuery(): void
    {
        $date = now();
        $start = $date->clone()->startOfDay()->timestamp;
        $end = $date->clone()->endOfDay()->timestamp;

        Game::whereDate('first_release_date', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where (first_release_date >= {$start} & first_release_date <= {$end});"));
    }

    public function testItShouldGenerateWhereDateLargerOrEqualsQuery(): void
    {
        $date = now();
        $start = $date->clone()->startOfDay()->timestamp;

        Game::whereDate('first_release_date', '>=', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date >= {$start};"));
    }

    public function testItShouldGenerateWhereDateLargerQuery(): void
    {
        $date = now();
        $end = $date->clone()->addDay()->startOfDay()->timestamp;

        Game::whereDate('first_release_date', '>', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date > {$end};"));
    }

    public function testItShouldGenerateWhereDateSmallerOrEqualsQuery(): void
    {
        $date = now();
        $end = $date->clone()->endOfDay()->timestamp;

        Game::whereDate('first_release_date', '<=', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date <= {$end};"));
    }

    public function testItShouldGenerateWhereDateSmallerQuery(): void
    {
        $date = now();
        $start = $date->clone()->subDay()->endOfDay()->timestamp;

        Game::whereDate('first_release_date', '<', $date->format('Y-m-d'))->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date < {$start};"));
    }

    public function testItShouldGenerateWhereYearEqualsQuery(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;
        $end = $date->clone()->endOfYear()->timestamp;

        Game::whereYear('first_release_date', $date->year)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where (first_release_date >= {$start} & first_release_date <= {$end});"));
    }

    public function testItShouldGenerateWhereYearLargerOrEqualsQuery(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;

        Game::whereYear('first_release_date', '>=', $date->year)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date >= {$start};"));
    }

    public function testItShouldGenerateWhereYearLargerQuery(): void
    {
        $date = now();
        $end = $date->clone()->endOfYear()->timestamp;

        Game::whereYear('first_release_date', '>', $date->year)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date > {$end};"));
    }

    public function testItShouldGenerateWhereYearSmallerOrEqualsQuery(): void
    {
        $date = now();
        $end = $date->clone()->endOfYear()->timestamp;

        Game::whereYear('first_release_date', '<=', $date->year)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date <= {$end};"));
    }

    public function testItShouldGenerateWhereYearSmallerQuery(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;

        Game::whereYear('first_release_date', '<', $date->year)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "where first_release_date < {$start};"));
    }

    public function testItShouldGenerateWhereHasQuery(): void
    {
        Game::whereHas('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where first_release_date != null;'));
    }

    public function testItShouldGenerateWhereHasNotQuery(): void
    {
        Game::whereHasNot('first_release_date')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where first_release_date = null;'));
    }

    public function testItShouldGenerateNestedWhereQuery(): void
    {
        Game::where('name', 'Fortnite')
            ->orWhere(function ($query): void {
                $query->where('aggregated_rating', '>=', 90)
                    ->where('aggregated_rating_count', '>=', 3000);
            })->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where name = "Fortnite" | (aggregated_rating >= 90 & aggregated_rating_count >= 3000);'));
    }

    public function testItShouldGenerateArrayWhereQuery(): void
    {
        Game::where([['name', 'Fortnite'], ['name', 'Borderlands 2']])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'where (name = "Fortnite" & name = "Borderlands 2");'));
    }

    public function testItShouldGenerateOrderbyQuery(): void
    {
        Game::orderBy('name')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'sort name asc;'));

        Game::orderBy('name', 'desc')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'sort name desc;'));

        Game::orderBy('name', 'asc')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'sort name asc;'));
    }

    public function testItShouldNotGenerateOrderbyQueryWithInvalidParams(): void
    {
        $this->expectException(InvalidParamsException::class);

        Game::orderBy('name', 'foo')->get();

        Http::assertNotSent(fn (Request $request) => $this->isApiCall($request, 'games', 'sort name foo;'));
    }

    public function testItShouldGenerateOrderbydescQuery(): void
    {
        Game::orderByDesc('name')->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'sort name desc;'));
    }

    public function testItShouldGenerateOffsetQuery(): void
    {
        Game::skip(10)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'offset 10;'));

        Game::offset(10)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'offset 10;'));
    }

    public function testItShouldGenerateLimitQuery(): void
    {
        Game::take(10)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'limit 10;'));

        Game::limit(10)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'limit 10;'));
    }

    public function testItShouldGenerateForPageQuery(): void
    {
        Game::forPage(2, 20)->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 20;\noffset 20;"));
    }

    public function testItShouldGenerateAllQuery(): void
    {
        Game::all();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 500;\noffset 0;"));
    }

    public function testItShouldGenerateFirstQuery(): void
    {
        Game::first();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 1;\noffset 0;"));
    }

    public function testItShouldGenerateFindQuery(): void
    {
        Game::find(1905);

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 1;\noffset 0;\nwhere id = 1905;"));
    }

    public function testItShouldGeneratePaginateQuery(): void
    {
        Game::paginate(20);

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 20;\noffset 0;"));

        $this->get('/?page=2');

        Game::paginate(20);

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', "limit 20;\noffset 20;"));
    }

    public function testItShouldGenerateExpandingQuery(): void
    {
        Game::with(['cover', 'artworks'])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'fields *,cover.*,artworks.*;'));

        Game::with(['involved_companies', 'involved_companies.company'])->get();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games', 'fields *,involved_companies.*,involved_companies.company.*;'));
    }

    public function testItShouldRequestCountEndpoint(): void
    {
        $count = Game::count();

        Http::assertSent(fn (Request $request) => $this->isApiCall($request, 'games/count', 'fields *;'));

        self::assertEquals(1337, $count);
    }

    public function testItShouldThrowExceptionForFirstOrFailMethod(): void
    {
        $this->expectException(ModelNotFoundException::class);

        Game::firstOrFail();
    }

    public function testItShouldThrowExceptionForFindOrFailMethod(): void
    {
        $this->expectException(ModelNotFoundException::class);

        Game::findOrFail(1337);
    }

    public function testItShouldThrowExceptionWhenBadMethodIsCalled(): void
    {
        $this->expectException(BadMethodCallException::class);

        Game::foo();
    }
}
