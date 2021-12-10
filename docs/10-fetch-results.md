# Fetch results

## Get

To finally get results for the query, simply call `get`:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::get();
```

## All

If you just want to get "all" results (limited to a maximum of 500)
just call the `all`-Method directly on your model:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::all();
```

## First

If you only want one result call the `first`-method after your query:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::first();
```

## Find

If you know the Identifier of the model you can simply call the `find`-method
with the identifier as a parameter:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::find(1905);
```

### FindOrFail

`find` returns `null` if no result were found. If you want to throw an Exception
instead use `findOrFail`. This will throw an
`MarcReichel\IGDBLaravel\Exceptions\ModelNotFoundException` if no result were
found.
