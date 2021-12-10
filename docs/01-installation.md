# Installation

## Create Twitch Developer App

[Create](https://dev.twitch.tv/console/apps/create) a new Twitch Developer App and set the `Client ID` and
`Client Secret` as described below.

## Install the package

You can install this package via composer using:

```bash
// torchlight! {"lineNumbers": false}
composer require marcreichel/igdb-laravel
```

The package will automatically register its service provider.

To publish the config file to `config/igdb.php` run:

```bash
// torchlight! {"lineNumbers": false}
php artisan igdb:publish
```

This is the default content of the config file:

```php
// torchlight! {"lineNumbers": false}
<?php

return [
    /*
     * These are the credentials you got from https://dev.twitch.tv/console/apps [tl! autolink]
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
    'webhook_secret' => env('IGDB_WEBHOOK_SECRET'),
];
```
