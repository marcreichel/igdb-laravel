# Ordering, Limit, & Offset

## orderBy

The `orderBy` method allows you to sort the result of the query by a given field.
The first argument to the `orderBy` method should be the field you wish to sort
by, while the second argument controls the direction of the sort and may be either
`asc` or `desc`:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::orderBy('first_release_date', 'asc')->get();
```

## skip / take (limit / offset)

To limit the number of results returned from the query, or to skip a given
number of results in the query, you may use the `skip` and `take` methods (`take` is limited to a maximum of 500):

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::skip(10)->take(5)->get();
```

Alternatively, you may use the `limit` and `offset` methods:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::offset(10)->limit(5)->get();
```
