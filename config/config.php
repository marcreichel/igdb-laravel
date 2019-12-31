<?php

return [
    /*
     * This is the API Token you got from https://api.igdb.com
     */
    'api_token' => env('IGDB_TOKEN', ''),

    /*
     * This package caches queries automatically (for 1 hour per default).
     * Here you can set how long each query should be cached (in seconds).
     *
     * To turn cache off set this value to 0
     */
    'cache_lifetime' => env('IGDB_CACHE_LIFETIME', 3600),

    /*
     * This is the per-page limit for your tier.
     */
    'per_page_limit' => 500,

    /*
     * This is the offset limit for your tier.
     */
    'offset_limit' => 5000,
];
