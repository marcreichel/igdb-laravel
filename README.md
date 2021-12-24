<h1 align="center">Laravel IGDB Wrapper</h1>

<p align="center">
    <a href="https://packagist.org/packages/marcreichel/igdb-laravel">
        <img src="https://img.shields.io/packagist/v/marcreichel/igdb-laravel" alt="Packagist Version">
    </a>
    <a href="https://packagist.org/packages/marcreichel/igdb-laravel">
        <img src="https://img.shields.io/packagist/dt/marcreichel/igdb-laravel" alt="Packagist Downloads">
    </a>
    <a href="https://github.com/marcreichel/igdb-laravel/actions/workflows/tests.yml">
        <img src="https://github.com/marcreichel/igdb-laravel/actions/workflows/tests.yml/badge.svg?event=push" alt="Tests">
    </a>
    <a href="https://github.com/marcreichel/igdb-laravel/actions/workflows/code-quality.yml">
        <img src="https://github.com/marcreichel/igdb-laravel/actions/workflows/code-quality.yml/badge.svg?event=push" alt="PHPStan">
    </a>
    <a href="https://www.codefactor.io/repository/github/marcreichel/igdb-laravel">
        <img src="https://www.codefactor.io/repository/github/marcreichel/igdb-laravel/badge" alt="CodeFactor">
    </a>
    <a href="https://codecov.io/gh/marcreichel/igdb-laravel">
        <img src="https://codecov.io/gh/marcreichel/igdb-laravel/branch/main/graph/badge.svg?token=m6FOB0CyPE" alt="codecov">
    </a>
    <a href="https://packagist.org/packages/marcreichel/igdb-laravel">
        <img src="https://img.shields.io/github/license/marcreichel/igdb-laravel" alt="License">
    </a>
    <a href="https://gitmoji.dev">
        <img src="https://img.shields.io/badge/gitmoji-%20😜%20😍-FFDD67.svg" alt="Gitmoji">
    </a>
</p>

<p align="center">
    This is a Laravel wrapper for version 4 of the <a href="https://api-docs.igdb.com/">IGDB API</a> (Apicalypse)
    including <a href="https://marcreichel.dev/docs/igdb-laravel/webhooks">webhook handling</a> since version 2.3.0.
</p>

![Cover](docs/art/cover.png)

## Basic installation

You can install this package via composer using:

```bash
composer require marcreichel/igdb-laravel
```

The package will automatically register its service provider.

To publish the config file to `config/igdb.php` run:

```bash
php artisan igdb:publish
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

- Restructure/Optimize Builder class for better code quality

## Contribution

Pull requests are welcome :)
