# IGDB Laravel Wrapper

This is a Laravel-Wrapper for version 3 of the [IGDB API](https://api.igdb.com/) (Apicalypse).

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
```

## Usage

If you're familiar with the [Eloquent System](https://laravel.com/docs/master/eloquent)
and the [Query Builder](https://laravel.com/docs/master/queries) of Laravel you
will love this package as it uses a similar approach.

### Models

Each endpoint of the API is mapped to its own model.

To get a list of games you simply call something like this:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::where('first_release_date', '>=', 1546297200)->get();
```

Here's a list of all available Models:

- Achievement
- AchievementIcon
- AgeRating
- AgeRatingContentDescription
- AlternativeName
- Artwork
- Character
- CharacterMugShot
- Collection
- Company
- CompanyLogo
- CompanyWebsite
- Cover
- ExternalGame
- Feed
- Franchise
- Game
- GameEngine
- GameEngineLogo
- GameMode
- GameVersion
- GameVersionFeature
- GameVersionFeatureValue
- GameVideo
- Genre
- InvolvedCompany
- Keyword
- MultiplayerMode
- Page
- PageBackground
- PageLogo
- PageWebsite
- Platform
- PlatformLogo
- PlatformVersion
- PlatformVersionCompany
- PlatformVersionReleaseDate
- PlatformWebsite
- PlayerPerspective
- ProductFamily
- Pulse
- PulseGroup
- PulseSource
- PulseUrl
- ReleaseDate
- Screenshot
- Search
- Theme
- TimeToBeat
- Title
- Website

### Query Builder

You can use one of the defined models listed above. The search results will be
mapped into the used model automatically then. This method is used in the
examples below.

Otherwise you can use the Query Builder itself like this:

```php
use MarcReichel\IGDBLaravel\Builder as IGDB;

$igdb = new IGDB('games'); // 'games' is the endpoint

$games = $igdb->get();
```

#### Select

Select which fields should be in the response. If you want to have all available
fields in the response you can also skip this method as the query builder will
select `*` by default. (**Attention**: This is the opposite behaviour from the
Apicalypse API)

```php
$games = Game::select(['*'])->get();

$games = Game::select(['name', 'first_release_date'])->get();
```

#### Search

```php
$games = Game::search('Fortnite')->get();
```

#### Where-Clauses

##### Simple Where Clauses

```php
$games = Game::where('first_release_date', '>=', 1546297200)->get();
```

For convenience, if you want to verify that a column is equal to a given value,
you may pass the value directly as the second argument to the `where` method:

```php
$games = Game::where('name', 'Fortnite')->get();
```

##### Or Statements

You may chain where constraints together as well as add `or` clauses to the query.
The `orWhere` method accepts the same arguments as the `where` method:

```php
$games = Game::where('name', 'Fortnite')->orWhere('name', 'Borderlands 2')->get();
```

##### Additional Where Clauses

###### whereBetween

The `whereBetween` method verifies that a fields's value is between two values:

```php
$games = Game::whereBetween('first_release_date', 1546297200, 1577833199)->get();
```

###### whereNotBetween

The `whereNotBetween` method verifies that a field's value lies outside of two
values:

```php
$games = Game::whereNotBetween('first_release_date', 1546297200, 1577833199)->get();
```

###### whereIn

The `whereIn` method verifies that a given field's value is contained within the
given array:

```php
$games = Game::whereIn('category', [0,4])->get();
```

###### whereNotIn

The `whereNotIn` method verifies that the given field's value is **not** 
contained in the given array:

```php
$games = Game::whereNotIn('category', [0,4])->get();
```

###### whereInAll / whereNotInAll / whereInExact / whereNotInExact

Alternatively you could use one of these methods to match against **all** or **exactly** the given array. 

###### whereNull

The `whereNull` method verifies that the value of the given field is `NULL`:

```php
$games = Game::whereNull('first_release_date')->get();
```

###### whereNotNull

The `whereNotNull` method verifies that the field's value is **not** `NULL`:

```php
$games = Game::whereNotNull('first_release_date')->get();
```

###### whereDate

The `whereDate` method may be used to compare a field's value against a date:

```php
$games = Game::whereDate('first_release_date', '2019-01-01')->get();
```

###### whereYear

The `whereYear` method may be used to compare a fields's value against a specific 
year:

```php
$games = Game::whereYear('first_release_date', 2019)->get();
```

###### whereHas / whereHasNot

These methods have the same syntax as `whereNull` and `whereNotNull` and literally
do the exact same thing. 

##### Parameter Grouping

```php
$games = Game::where('name', 'Fortnite')
    ->orWhere(function($query) {
        $query->where('aggregated_rating', '>=', 90)
            ->where('aggregated_rating_count', '>=', 3000);
    })->get();
```

#### Ordering, Limit, & Offset

##### orderBy

The `orderBy` method allows you to sort the result of the query by a given field.
The first argument to the `orderBy` method should be the field you wish to sort
by, while the second argument controls the direction of the sort and may be either
`asc` or `desc`:

```php
$games = Game::orderBy('first_release_date', 'asc')->get();
```

##### skip / take

To limit the number of results returned from the query, or to skip a given
number of results in the query, you may use the `skip` and `take` methods (Both
methods are limited to your current tier, so make sure you configure them
correctly in the config file):

```php
$games = Game::skip(10)->take(5)->get();
```

Alternatively, you may use the `limit` and `offset` methods:

```php
$games = Game::offset(10)->limit(5)->get();
```

#### Cache

You can overwrite the default cache time for one specific query. So you can for
example turn off caching for a query:

```php
$games = Game::cache(0)->get();
```

#### Get

To finally get results for the query, simply call `get`:

```php
$games = Game::get();
```

#### All

If you just want to get "all" results (limited to the per_page_limit of your tier)
just call the `all`-Method directly on your model:

```php
$games = Game::all();
```

#### First

If you only want one result call the `first`-method after your query:

```php
$game = Game::first();
```

#### Find

If you know the Identifier of the model you can simply call the `find`-method
with the identifier as a parameter:

```php
$game = Game::find(1905);
```

##### FindOrFail

`find` returns `null` if no result were found. If you want to throw an Exception
instead use `findOrFail`. This will throw an
`MarcReichel\IGDBLaravel\Exceptions\ModelNotFoundException` if no result were
found.

#### Relationships (Extends)

To extend your result use the `with`-method:

```php
$game = Game::with(['cover', 'artworks'])->get();
```

By default, every field (`*`) of the relationship is selected.
If you want to define the fields of the relationship yourself you have to define
the relationship as the array-key and the fields as an array:

```php
$game = Game::with(['cover' => ['url', 'image_id'])->get();
```

### Reading properties

#### Model-based approach

If you used the Model-based approach you can simply get a property:

```php
$game = Game::find(1905);

if ($game) {
    echo $game->name; // Will output "Fortnite"
}
```

If you want to access a property which does not exist `null` is returned:

```php
$game = Game::find(1905);

if ($game) {
    echo $game->foo; // Will output nothing
}
```

#### Query-Builder-based approach

If you used the Query Builder itself you must check if a property exists
yourself.

## TODO List

- Refactor code (beautify code)
- Write unit tests

## Contribution

Pull requests are welcome :)
