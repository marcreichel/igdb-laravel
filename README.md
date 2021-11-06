# Laravel IGDB Wrapper

[![Packagist Version](https://img.shields.io/packagist/v/marcreichel/igdb-laravel)](https://packagist.org/packages/marcreichel/igdb-laravel)
[![Packagist Downloads](https://img.shields.io/packagist/dt/marcreichel/igdb-laravel)](https://packagist.org/packages/marcreichel/igdb-laravel)
[![tests](https://github.com/marcreichel/igdb-laravel/actions/workflows/tests.yml/badge.svg?event=push)](https://github.com/marcreichel/igdb-laravel/actions/workflows/tests.yml)
[![PHPStan](https://github.com/marcreichel/igdb-laravel/actions/workflows/code-quality.yml/badge.svg?event=push)](https://github.com/marcreichel/igdb-laravel/actions/workflows/code-quality.yml)
[![CodeFactor](https://www.codefactor.io/repository/github/marcreichel/igdb-laravel/badge)](https://www.codefactor.io/repository/github/marcreichel/igdb-laravel)
[![codecov](https://codecov.io/gh/marcreichel/igdb-laravel/branch/main/graph/badge.svg?token=m6FOB0CyPE)](https://codecov.io/gh/marcreichel/igdb-laravel)
[![GitHub](https://img.shields.io/github/license/marcreichel/igdb-laravel)](https://packagist.org/packages/marcreichel/igdb-laravel)
[![Gitmoji](https://img.shields.io/badge/gitmoji-%20😜%20😍-FFDD67.svg)](https://gitmoji.dev)

This is a Laravel wrapper for version 4 of the [IGDB API](https://api-docs.igdb.com/) (Apicalypse)
including [webhook handling](#-webhooks-since-v230) since version 2.3.0.

## Basic installation

You can install this package via composer using:

```bash
composer require marcreichel/igdb-laravel
```

The package will automatically register its service provider.

To publish the config file to `config/igdb.php` run:

```bash
php artisan vendor:publish --provider="MarcReichel\IGDBLaravel\IGDBLaravelServiceProvider"
```

This is the default content of the config file:

```php
return [
    /*
     * These are the credentials you got from https://dev.twitch.tv/console/apps
     */
    'credentials' => [
        'client_id' => env('TWITCH_CLIENT_ID', ''),
        'client_secret' => env('TWITCH_CLIENT_SECRET', ''),
    ],

    /*
     * This package caches queries automatically (for 1 hour per default).
     * Here you can set how long each query should be cached (in seconds).
     *
     * To turn cache off set this value to 0
     */
    'cache_lifetime' => env('IGDB_CACHE_LIFETIME', 3600),

    /*
     * Path where the webhooks should be handled.
     */
    'webhook_path' => 'igdb-webhook/handle',

    /*
     * The webhook secret.
     *
     * This needs to be a string of your choice in order to use the webhook
     * functionality.
     */
    'webhook_secret' => env('IGDB_WEBHOOK_SECRET', null),
];
```

## Documentation

You will find the full documentation on [the dedicated documentation site](https://marcreichel.dev/docs/igdb-laravel).

## Testing

Run the tests with:

```bash
composer test
```

## Roadmap

- Clean up and restructure/optimize Docs
- Restructure/Optimize Builder class for better code quality

## Contribution

Pull requests are welcome :)
