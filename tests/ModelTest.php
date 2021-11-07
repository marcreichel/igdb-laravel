<?php

namespace MarcReichel\IGDBLaravel\Tests;

use BadMethodCallException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;
use MarcReichel\IGDBLaravel\Exceptions\ModelNotFoundException;
use MarcReichel\IGDBLaravel\Models\Game;

class ModelTest extends TestCase
{
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
    }

    /** @test */
    public function it_should_generate_fields_query(): void
    {
        Game::select(['name'])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'fields name;');
        });
    }

    /** @test */
    public function it_should_generate_search_query(): void
    {
        Game::search('Fortnite')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'search "Fortnite";');
        });
    }

    /** @test */
    public function it_should_generate_where_equals_query_with_operator(): void
    {
        Game::where('name', '=', 'Fortnite')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite";');
        });

        Game::where('first_release_date', '>=', 1546297200)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date >= 1546297200;');
        });
    }

    /** @test */
    public function it_should_generate_where_equals_query_without_operator(): void
    {
        Game::where('name', 'Fortnite')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite";');
        });
    }

    /** @test */
    public function it_should_generate_and_where_query(): void
    {
        Game::where('name', 'Fortnite')->where('name', 'Borderlands 2')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" & name = "Borderlands 2";');
        });
    }

    /** @test */
    public function it_should_generate_or_where_query(): void
    {
        Game::where('name', 'Fortnite')->orWhere('name', 'Borderlands 2')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | name = "Borderlands 2";');
        });
    }

    /** @test */
    public function it_should_generate_where_between_query(): void
    {
        Game::whereBetween('first_release_date', 1546297200, 1577833199)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where (first_release_date >= 1546297200 & first_release_date <= 1577833199);');
        });
    }

    /** @test */
    public function it_should_generate_where_between_query_without_boundaries(): void
    {
        Game::whereBetween('first_release_date', 1546297200, 1577833199, false)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where (first_release_date > 1546297200 & first_release_date < 1577833199);');
        });
    }

    /** @test */
    public function it_should_generate_where_in_query(): void
    {
        Game::whereIn('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category = (0,4);');
        });
    }

    /** @test */
    public function it_should_generate_where_in_exact_query(): void
    {
        Game::whereInExact('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category = {0,4};');
        });
    }

    /** @test */
    public function it_should_generate_where_in_all_query(): void
    {
        Game::whereInAll('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category = [0,4];');
        });
    }

    /** @test */
    public function it_should_generate_where_not_in_query(): void
    {
        Game::whereNotIn('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category != (0,4);');
        });
    }

    /** @test */
    public function it_should_generate_where_not_in_exact_query(): void
    {
        Game::whereNotInExact('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category != {0,4};');
        });
    }

    /** @test */
    public function it_should_generate_where_not_in_all_query(): void
    {
        Game::whereNotInAll('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category != [0,4];');
        });
    }

    /** @test */
    public function it_should_generate_where_null_query(): void
    {
        Game::whereNull('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date = null;');
        });
    }

    /** @test */
    public function it_should_generate_where_not_null_query(): void
    {
        Game::whereNotNull('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date != null;');
        });
    }

    /** @test */
    public function it_should_generate_where_date_equals_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfDay()->timestamp;
        $end = $date->clone()->endOfDay()->timestamp;

        Game::whereDate('first_release_date', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall($request, 'games', "where (first_release_date >= {$start} & first_release_date <= {$end});");
        });
    }

    /** @test */
    public function it_should_generate_where_date_larger_or_equals_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfDay()->timestamp;

        Game::whereDate('first_release_date', '>=', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where first_release_date >= {$start};");
        });
    }

    /** @test */
    public function it_should_generate_where_date_larger_query(): void
    {
        $date = now();
        $end = $date->clone()->addDay()->startOfDay()->timestamp;

        Game::whereDate('first_release_date', '>', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date > {$end};");
        });
    }

    /** @test */
    public function it_should_generate_where_date_smaller_or_equals_query(): void
    {
        $date = now();
        $end = $date->clone()->endOfDay()->timestamp;

        Game::whereDate('first_release_date', '<=', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date <= {$end};");
        });
    }

    /** @test */
    public function it_should_generate_where_date_smaller_query(): void
    {
        $date = now();
        $start = $date->clone()->subDay()->endOfDay()->timestamp;

        Game::whereDate('first_release_date', '<', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where first_release_date < {$start};");
        });
    }

    /** @test */
    public function it_should_generate_where_year_equals_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;
        $end = $date->clone()->endOfYear()->timestamp;

        Game::whereYear('first_release_date', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall($request, 'games', "where (first_release_date >= {$start} & first_release_date <= {$end});");
        });
    }

    /** @test */
    public function it_should_generate_where_year_larger_or_equals_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;

        Game::whereYear('first_release_date', '>=', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where first_release_date >= {$start};");
        });
    }

    /** @test */
    public function it_should_generate_where_year_larger_query(): void
    {
        $date = now();
        $end = $date->clone()->endOfYear()->timestamp;

        Game::whereYear('first_release_date', '>', $date->year)->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date > {$end};");
        });
    }

    /** @test */
    public function it_should_generate_where_year_smaller_or_equals_query(): void
    {
        $date = now();
        $end = $date->clone()->endOfYear()->timestamp;

        Game::whereYear('first_release_date', '<=', $date->year)->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date <= {$end};");
        });
    }

    /** @test */
    public function it_should_generate_where_year_smaller_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;

        Game::whereYear('first_release_date', '<', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where first_release_date < {$start};");
        });
    }

    /** @test */
    public function it_should_generate_where_has_query(): void
    {
        Game::whereHas('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date != null;');
        });
    }

    /** @test */
    public function it_should_generate_where_has_not_query(): void
    {
        Game::whereHasNot('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date = null;');
        });
    }

    /** @test */
    public function it_should_generate_nested_where_query(): void
    {
        Game::where('name', 'Fortnite')
            ->orWhere(function($query) {
                $query->where('aggregated_rating', '>=', 90)
                    ->where('aggregated_rating_count', '>=', 3000);
            })->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | (aggregated_rating >= 90 & aggregated_rating_count >= 3000);');
        });
    }

    /** @test */
    public function it_should_generate_array_where_query(): void
    {
        Game::where([['name', 'Fortnite'], ['name', 'Borderlands 2']])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where (name = "Fortnite" & name = "Borderlands 2");');
        });
    }

    /** @test */
    public function it_should_generate_orderby_query(): void
    {
        Game::orderBy('name')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name asc;');
        });

        Game::orderBy('name', 'desc')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name desc;');
        });

        Game::orderBy('name', 'asc')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name asc;');
        });
    }

    /** @test */
    public function it_should_not_generate_orderby_query_with_invalid_params(): void
    {
        $this->expectException(InvalidParamsException::class);

        Game::orderBy('name', 'foo')->get();

        Http::assertNotSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name foo;');
        });
    }

    /** @test */
    public function it_should_generate_orderbydesc_query(): void
    {
        Game::orderByDesc('name')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name desc;');
        });
    }

    /** @test */
    public function it_should_generate_offset_query(): void
    {
        Game::skip(10)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'offset 10;');
        });

        Game::offset(10)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'offset 10;');
        });
    }

    /** @test */
    public function it_should_generate_limit_query(): void
    {
        Game::take(10)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'limit 10;');
        });

        Game::limit(10)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'limit 10;');
        });
    }

    /** @test */
    public function it_should_generate_forPage_query(): void
    {
        Game::forPage(2, 20)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 20;\noffset 20;");
        });
    }

    /** @test */
    public function it_should_generate_all_query(): void
    {
        Game::all();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 500;\noffset 0;");
        });
    }

    /** @test */
    public function it_should_generate_first_query(): void
    {
        Game::first();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 1;\noffset 0;");
        });
    }



    /** @test */
    public function it_should_generate_find_query(): void
    {
        Game::find(1905);

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 1;\noffset 0;\nwhere id = 1905;");
        });
    }

    /** @test */
    public function it_should_generate_paginate_query(): void
    {
        Game::paginate(20);

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 20;\noffset 0;");
        });

        $this->get('/?page=2');

        Game::paginate(20);

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 20;\noffset 20;");
        });
    }

    /** @test */
    public function it_should_generate_expanding_query(): void
    {
        Game::with(['cover', 'artworks'])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "fields *,cover.*,artworks.*;");
        });

        Game::with(['involved_companies', 'involved_companies.company'])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "fields *,involved_companies.*,involved_companies.company.*;");
        });
    }

    /** @test */
    public function it_should_request_count_endpoint(): void
    {
        $count = Game::count();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games/count', 'fields *;');
        });

        self::assertEquals(1337, $count);
    }

    /**
     * @test
     */
    public function it_should_throw_exception_for_first_or_fail_method(): void
    {
        $this->expectException(ModelNotFoundException::class);

        Game::firstOrFail();
    }

    /**
     * @test
     */
    public function it_should_throw_exception_for_find_or_fail_method(): void
    {
        $this->expectException(ModelNotFoundException::class);

        Game::findOrFail(1337);
    }

    /** @test */
    public function it_should_throw_exception_when_bad_method_is_called(): void
    {
        $this->expectException(BadMethodCallException::class);

        Game::foo();
    }
}
