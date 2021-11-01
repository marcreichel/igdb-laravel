# Laravel IGDB Wrapper

[![Packagist Version](https://img.shields.io/packagist/v/marcreichel/igdb-laravel)](https://packagist.org/packages/marcreichel/igdb-laravel)
[![Packagist Downloads](https://img.shields.io/packagist/dt/marcreichel/igdb-laravel)](https://packagist.org/packages/marcreichel/igdb-laravel)
[![tests](https://github.com/marcreichel/igdb-laravel/actions/workflows/tests.yml/badge.svg?event=push)](https://github.com/marcreichel/igdb-laravel/actions/workflows/tests.yml)
[![PHPStan](https://github.com/marcreichel/igdb-laravel/actions/workflows/code-quality.yml/badge.svg?event=push)](https://github.com/marcreichel/igdb-laravel/actions/workflows/code-quality.yml)
[![CodeFactor](https://www.codefactor.io/repository/github/marcreichel/igdb-laravel/badge)](https://www.codefactor.io/repository/github/marcreichel/igdb-laravel)
[![codecov](https://codecov.io/gh/marcreichel/igdb-laravel/branch/master/graph/badge.svg?token=m6FOB0CyPE)](https://codecov.io/gh/marcreichel/igdb-laravel)
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

## Usage

If you're familiar with the [Eloquent System](https://laravel.com/docs/eloquent)
and the [Query Builder](https://laravel.com/docs/queries) of Laravel you will love this package as it uses a similar
approach.

### Models

Each endpoint of the API is mapped to its own model.

To get a list of games you simply call something like this:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::where('first_release_date', '>=', 1546297200)->get();
```

[Here](src/Models) you can find a list of all available Models.

### Query Builder

You can use one of the defined models listed above. The search results will be mapped into the used model automatically
then. This method is used in the examples below.

Otherwise you can use the Query Builder itself like this:

```php
use MarcReichel\IGDBLaravel\Builder as IGDB;

$igdb = new IGDB('games'); // 'games' is the endpoint

$games = $igdb->get();
```

#### Select

Select which fields should be in the response. If you want to have all available fields in the response you can also
skip this method as the query builder will select `*` by default. (**Attention**: This is the opposite behaviour from
the Apicalypse API)

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::select(['*'])->get();

$games = Game::select(['name', 'first_release_date'])->get();
```

#### Search

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::search('Fortnite')->get();
```

**Attention:** Searchable models are `Character`, `Collection`, `Game`, `Platform` and `Theme`.

#### Fuzzy Search ("where like" chain) (since v3.1.0)

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::fuzzySearch(
    // fields to search in
    [
        'name',
        'involved_companies.company.name', // you can search for nested values as well
    ],
    // the query to search for
    'Call of Duty',
    // enable/disable case sensitivity (disabled by default)
    false,
)->get();
```

**Attention**: Keep in mind you have to do the sorting of the results yourself. They are not ordered by relevance or
something like this.

#### Where-Clauses

##### Simple Where Clauses

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::where('first_release_date', '>=', 1546297200)->get();
```

For convenience, if you want to verify that a column is equal to a given value, you may pass the value directly as the
second argument to the `where` method:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::where('name', 'Fortnite')->get();
```

##### Or Statements

You may chain where constraints together as well as add `or` clauses to the query. The `orWhere` method accepts the same
arguments as the `where` method:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::where('name', 'Fortnite')->orWhere('name', 'Borderlands 2')->get();
```

##### Additional Where Clauses

###### whereBetween

The `whereBetween` method verifies that a fields's value is between two values:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereBetween('first_release_date', 1546297200, 1577833199)->get();
```

###### whereNotBetween

The `whereNotBetween` method verifies that a field's value lies outside of two values:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereNotBetween('first_release_date', 1546297200, 1577833199)->get();
```

###### whereIn

The `whereIn` method verifies that a given field's value is contained within the given array:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereIn('category', [0,4])->get();
```

###### whereNotIn

The `whereNotIn` method verifies that the given field's value is **not**
contained in the given array:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereNotIn('category', [0,4])->get();
```

###### whereInAll / whereNotInAll / whereInExact / whereNotInExact

Alternatively you could use one of these methods to match against **all** or **exactly** the given array.

###### whereNull

The `whereNull` method verifies that the value of the given field is `NULL`:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereNull('first_release_date')->get();
```

###### whereNotNull

The `whereNotNull` method verifies that the field's value is **not** `NULL`:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereNotNull('first_release_date')->get();
```

###### whereDate

The `whereDate` method may be used to compare a field's value against a date:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereDate('first_release_date', '2019-01-01')->get();
```

###### whereYear

The `whereYear` method may be used to compare a fields's value against a specific year:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereYear('first_release_date', 2019)->get();
```

###### whereHas / whereHasNot

These methods have the same syntax as `whereNull` and `whereNotNull` and literally do the exact same thing.

###### whereLike / whereNotLike

```php
use MarcReichel\IGDBLaravel\Models\Game;

// The third parameter activates/deactivates case sensitivity.

$games = Game::whereLike('name', 'fort*', false)->get();

// Will get all games where the name starts with "fort".

$games = Game::whereLike('name', '*nite', false)->get();

// Will get all games where the name ends with "nite".

$games = Game::whereLike('name', '*rtni*', false)->get();

// Will get all games where the name contains "rtni".

// Since version 3.1.0 the leading AND trailing
// asterisks can be omitted in this case.
```

##### Parameter Grouping

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::where('name', 'Fortnite')
    ->orWhere(function($query) {
        $query->where('aggregated_rating', '>=', 90)
            ->where('aggregated_rating_count', '>=', 3000);
    })->get();
```

#### Ordering, Limit & Offset

##### orderBy

The `orderBy` method allows you to sort the result of the query by a given field. The first argument to the `orderBy`
method should be the field you wish to sort by, while the second argument controls the direction of the sort and may be
either
`asc` or `desc`:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::orderBy('first_release_date', 'asc')->get();
```

##### skip / take

To limit the number of results returned from the query, or to skip a given number of results in the query, you may use
the `skip` and `take` methods (`take` is limited to a maximum of 500):

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::skip(10)->take(5)->get();
```

Alternatively, you may use the `limit` and `offset` methods:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::offset(10)->limit(5)->get();
```

#### Cache

You can overwrite the default cache time for one specific query. So you can for example turn off caching for a query:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::cache(0)->get();
```

#### Get

To finally get results for the query, simply call `get`:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::get();
```

#### All

If you just want to get "all" results (limited to a maximum of 500)
just call the `all`-Method directly on your model:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::all();
```

#### First

If you only want one result call the `first`-method after your query:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::first();
```

#### Find

If you know the Identifier of the model you can simply call the `find`-method with the identifier as a parameter:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::find(1905);
```

##### FindOrFail

`find` returns `null` if no result were found. If you want to throw an Exception instead use `findOrFail`. This will
throw an
`MarcReichel\IGDBLaravel\Exceptions\ModelNotFoundException` if no result were found.

#### Relationships (Extends)

To extend your result use the `with`-method:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::with(['cover', 'artworks'])->get();
```

By default, every field (`*`) of the relationship is selected. If you want to define the fields of the relationship
yourself you have to define the relationship as the array-key and the fields as an array:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::with(['cover' => ['url', 'image_id']])->get();
```

### Reading properties

#### Model-based approach

If you used the Model-based approach you can simply get a property:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::find(1905);

if ($game) {
    echo $game->name; // Will output "Fortnite"
}
```

If you want to access a property which does not exist `null` is returned:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::find(1905);

if ($game) {
    echo $game->foo; // Will output nothing
}
```

#### Query-Builder-based approach

If you used the Query Builder itself you must check if a property exists yourself.

## ✨ Webhooks (since v2.3.0)

Since version 2.3.0 of this package you can create webhooks and handle their requests with ease. 🎉

### Initial setup

#### Configuration

Inside your `config/igdb.php` file you need to have a `webhook_path` and `webhook_secret` of your choice like so (you
only need to create this if you upgraded from a prior version of this package. New installations have this configured
automatically):

```php
return [
    // ...
    // Other configs
    // ...
    
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

And then set a secret inside your `.env` file:

```dotenv
IGDB_WEBHOOK_SECRET=yoursecret
```

The secret can be any string you like. It is attached as a `X-Secret` HTTP header to every webhook request and is
verified by this package automatically. So make sure your defined secret matches the secret you created the webhooks
with.

**Important**: Make sure the `APP_URL` in your `.env` file is something different than `http://localhost` or
`http://127.0.0.1` as webhooks would not work then. The `APP_URL` is used when creating webhooks via the
[CLI](#manage-webhooks-via-cli). When [creating webhooks via code](#creating-a-webhook) the domain from the browser url
bar is used. So please make sure you are accessing your app via a non-localhost url.

And that wraps it up! You're all good to create your first webhook.

### Creating a webhook

Let's say we want to be informed whenever a new game is created on https://igdb.com.

First of all we need to inform IGDB that we want to be informed.

For this we create a webhook like so (for example inside a controller):

```php
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Models\Game;
use Illuminate\Routing\Controller;

class ExampleController extends Controller
{
    public function createWebhook()
    {
        Game::createWebhook(Method::CREATE)
    }
}
```

### Listen for events

Now that we have created our webhook we can listen for a specific event - in our case when a game is created.

For this we create a Laravel EventListener or for sake of simplicity we just listen for an event inside the `boot()`
method of our `app/providers/EventServiceProvider.php`:

```php
use MarcReichel\IGDBLaravel\Events\GameCreated;
use Illuminate\Support\Facades\Event;

public function boot()
{
    Event::listen(function (GameCreated $event) {
        // $event->data holds the (unexpanded!) data (of the game in this case)
    });
}
```

[Here](src/Events) you can find a list of all available events.

Further information on how to set up event listeners can be found on
the [official docs](https://laravel.com/docs/events).

### Manage webhooks via CLI

#### List your webhooks

```bash
$ php artisan igdb:webhooks
```

#### Create a webhook

```bash
$ php artisan igdb:webhooks:create {model?} {--method=}
```

You can also just call `php artisan igdb:webhooks:create` without any arguments. The command will then ask for the
required data interactively.

The `model` parameter needs to be the (studly cased) class name of a model (e.g. `Game`).

The `--method` option needs to be one of `create`, `update` or `delete` accordingly for which event you want to listen.

#### Reactivate a webhook

```bash
$ php artisan igdb:webhooks:reactivate {id}
```

For `{id}` insert the id of the (inactive) webhook.

#### Delete a webhook

```bash
$ php artisan igdb:webhooks:delete {id?} {--A|all}
```

You may provide the `id` of a webhook to delete it or use the `-A`/`--all` flag to delete all your registered webhooks.

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
