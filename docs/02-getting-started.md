# Getting started

If you're familiar with the [Eloquent System](https://laravel.com/docs/eloquent) and
the [Query Builder](https://laravel.com/docs/queries) of Laravel you will love this package as it uses a similar
approach.

## Models

Each endpoint of the API is mapped to its own model.

To get a list of games you simply call something like this:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::where('name', 'Fortnite')->get();
```

[Here](https://github.com/marcreichel/igdb-laravel/tree/main/src/Models) you can find a list of all available Models.

When you use one of these models the query results will be mapped into the used model automatically.

_This method is used in the examples in the documentation._

## Query Builder

You can also use the Query Builder (which is used under the hood) directly if you want to:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Builder as IGDB;

$igdb = new IGDB('games'); // 'games' is the endpoint

$games = $igdb->get();
```

