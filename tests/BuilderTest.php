<?php

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use MarcReichel\IGDBLaravel\Builder;
use MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException;

class BuilderTest extends TestCase
{
    private $igdb;

    public function setUp(): void
    {
        parent::setUp();

        $this->igdb = new Builder('games');
    }

    /** @test */
    public function it_should_generate_fields_query(): void
    {
        $this->igdb->select(['name'])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'fields name;');
        });
    }

    /** @test */
    public function it_should_generate_search_query(): void
    {
        $this->igdb->search('Fortnite')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'search "Fortnite";');
        });
    }

    /** @test */
    public function it_should_generate_where_equals_query_with_operator(): void
    {
        $this->igdb->where('name', '=', 'Fortnite')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite";');
        });

        $this->igdb->where('first_release_date', '>=', 1546297200)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date >= 1546297200;');
        });
    }

    /** @test */
    public function it_should_generate_where_equals_query_without_operator(): void
    {
        $this->igdb->where('name', 'Fortnite')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite";');
        });
    }

    /** @test */
    public function it_should_generate_and_where_query(): void
    {
        $this->igdb->where('name', 'Fortnite')->where('name', 'Borderlands 2')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" & name = "Borderlands 2";');
        });
    }

    /** @test */
    public function it_should_generate_or_where_query(): void
    {
        $this->igdb->where('name', 'Fortnite')->orWhere('name', 'Borderlands 2')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where name = "Fortnite" | name = "Borderlands 2";');
        });
    }

    /** @test */
    public function it_should_generate_where_between_query(): void
    {
        $this->igdb->whereBetween('first_release_date', 1546297200, 1577833199)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where (first_release_date >= 1546297200 & first_release_date <= 1577833199);');
        });
    }

    /** @test */
    public function it_should_generate_where_between_query_without_boundaries(): void
    {
        $this->igdb->whereBetween('first_release_date', 1546297200, 1577833199, false)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where (first_release_date > 1546297200 & first_release_date < 1577833199);');
        });
    }

    /** @test */
    public function it_should_generate_where_in_query(): void
    {
        $this->igdb->whereIn('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category = (0,4);');
        });
    }

    /** @test */
    public function it_should_generate_where_in_exact_query(): void
    {
        $this->igdb->whereInExact('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category = {0,4};');
        });
    }

    /** @test */
    public function it_should_generate_where_in_all_query(): void
    {
        $this->igdb->whereInAll('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category = [0,4];');
        });
    }

    /** @test */
    public function it_should_generate_where_not_in_query(): void
    {
        $this->igdb->whereNotIn('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category != (0,4);');
        });
    }

    /** @test */
    public function it_should_generate_where_not_in_exact_query(): void
    {
        $this->igdb->whereNotInExact('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category != {0,4};');
        });
    }

    /** @test */
    public function it_should_generate_where_not_in_all_query(): void
    {
        $this->igdb->whereNotInAll('category', [0,4])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where category != [0,4];');
        });
    }

    /** @test */
    public function it_should_generate_where_null_query(): void
    {
        $this->igdb->whereNull('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date = null;');
        });
    }

    /** @test */
    public function it_should_generate_where_not_null_query(): void
    {
        $this->igdb->whereNotNull('first_release_date')->get();

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

        $this->igdb->whereDate('first_release_date', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall($request, 'games', "where (first_release_date >= {$start} & first_release_date <= {$end});");
        });
    }

    /** @test */
    public function it_should_generate_where_date_larger_or_equals_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '>=', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where first_release_date >= {$start};");
        });
    }

    /** @test */
    public function it_should_generate_where_date_larger_query(): void
    {
        $date = now();
        $end = $date->clone()->addDay()->startOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '>', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date > {$end};");
        });
    }

    /** @test */
    public function it_should_generate_where_date_smaller_or_equals_query(): void
    {
        $date = now();
        $end = $date->clone()->endOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '<=', $date->format('Y-m-d'))->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date <= {$end};");
        });
    }

    /** @test */
    public function it_should_generate_where_date_smaller_query(): void
    {
        $date = now();
        $start = $date->clone()->subDay()->endOfDay()->timestamp;

        $this->igdb->whereDate('first_release_date', '<', $date->format('Y-m-d'))->get();

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

        $this->igdb->whereYear('first_release_date', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start, $end) {
            return $this->isApiCall($request, 'games', "where (first_release_date >= {$start} & first_release_date <= {$end});");
        });
    }

    /** @test */
    public function it_should_generate_where_year_larger_or_equals_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '>=', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where first_release_date >= {$start};");
        });
    }

    /** @test */
    public function it_should_generate_where_year_larger_query(): void
    {
        $date = now();
        $end = $date->clone()->endOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '>', $date->year)->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date > {$end};");
        });
    }

    /** @test */
    public function it_should_generate_where_year_smaller_or_equals_query(): void
    {
        $date = now();
        $end = $date->clone()->endOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '<=', $date->year)->get();

        Http::assertSent(function (Request $request) use ($end) {
            return $this->isApiCall($request, 'games', "where first_release_date <= {$end};");
        });
    }

    /** @test */
    public function it_should_generate_where_year_smaller_query(): void
    {
        $date = now();
        $start = $date->clone()->startOfYear()->timestamp;

        $this->igdb->whereYear('first_release_date', '<', $date->year)->get();

        Http::assertSent(function (Request $request) use ($start) {
            return $this->isApiCall($request, 'games', "where first_release_date < {$start};");
        });
    }

    /** @test */
    public function it_should_generate_where_has_query(): void
    {
        $this->igdb->whereHas('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date != null;');
        });
    }

    /** @test */
    public function it_should_generate_where_has_not_query(): void
    {
        $this->igdb->whereHasNot('first_release_date')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where first_release_date = null;');
        });
    }

    /** @test */
    public function it_should_generate_nested_where_query(): void
    {
        $this->igdb->where('name', 'Fortnite')
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
        $this->igdb->where([['name', 'Fortnite'], ['name', 'Borderlands 2']])->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'where (name = "Fortnite" & name = "Borderlands 2");');
        });
    }

    /** @test */
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

        $this->igdb->orderBy('name', 'asc')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name asc;');
        });
    }

    /** @test */
    public function it_should_not_generate_orderby_query_with_invalid_params(): void
    {
        $this->expectException(InvalidParamsException::class);

        $this->igdb->orderBy('name', 'foo')->get();

        Http::assertNotSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name foo;');
        });
    }

    /** @test */
    public function it_should_generate_orderbydesc_query(): void
    {
        $this->igdb->orderByDesc('name')->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', 'sort name desc;');
        });
    }

    /** @test */
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

    /** @test */
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

    /** @test */
    public function it_should_generate_forPage_query(): void
    {
        $this->igdb->forPage(2, 20)->get();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 20;\noffset 20;");
        });
    }

    /** @test */
    public function it_should_generate_all_query(): void
    {
        $this->igdb->all();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 500;\noffset 0;");
        });
    }

    /** @test */
    public function it_should_generate_first_query(): void
    {
        $this->igdb->first();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 1;\noffset 0;");
        });
    }



    /** @test */
    public function it_should_generate_find_query(): void
    {
        $this->igdb->find(1905);

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games', "limit 1;\noffset 0;\nwhere id = 1905;");
        });
    }

    /** @test */
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

    /** @test */
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
    }

    /** @test */
    public function it_should_request_count_endpoint(): void
    {
        $count = $this->igdb->count();

        Http::assertSent(function (Request $request) {
            return $this->isApiCall($request, 'games/count', 'fields *;');
        });

        self::assertEquals(1337, $count);
    }
}
