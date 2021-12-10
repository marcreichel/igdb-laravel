# Where clauses

## Simple where clause

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::where('first_release_date', '>=', now()->subMonth())
    ->get();
```

> **Please note**: `Carbon` objects are supported since v3.4.0.

For convenience, if you want to verify that a column is equal to a given value, you may pass the value directly as the
second argument to the `where` method:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::where('name', 'Fortnite')->get();

// this is the same as

$games = Game::where('name', '=', 'Fortnite')->get();
```

## OR statements

You may chain where constraints together as well as add `or` clauses to the query. The `orWhere` method accepts the same
arguments as the where method:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::where('name', 'Fortnite')
             ->orWhere('name', 'Borderlands 2')
             ->get();
```

## Additional Where Clauses

### whereBetween

The `whereBetween` method verifies that a fields's value is between two values:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereBetween('first_release_date', now()->subYear(), now())
             ->get();
```

> **Please note**: `Carbon` objects are supported since v3.4.0.

### whereNotBetween

The `whereNotBetween` method verifies that a field's value lies outside of two
values:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereNotBetween('first_release_date', now()->subYear(), now())
             ->get();
```

> **Please note**: `Carbon` objects are supported since v3.4.0.

### whereIn

The `whereIn` method verifies that a given field's value is contained within the
given array:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereIn('category', [0,4])->get();
```

### whereNotIn

The `whereNotIn` method verifies that the given field's value is **not**
contained in the given array:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereNotIn('category', [0,4])->get();
```

### whereInAll / whereNotInAll / whereInExact / whereNotInExact

Alternatively you could use one of these methods to match against **all** or **exactly** the given array.

### whereNull

The `whereNull` method verifies that the value of the given field is `NULL`:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereNull('first_release_date')->get();
```

### whereNotNull

The `whereNotNull` method verifies that the field's value is **not** `NULL`:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereNotNull('first_release_date')->get();
```

### whereDate

The `whereDate` method may be used to compare a field's value against a date:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereDate('first_release_date', '2019-01-01')
             ->get();
```

### whereYear

The `whereYear` method may be used to compare a fields's value against a specific
year:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::whereYear('first_release_date', 2019)
             ->get();
```

### whereHas / whereHasNot

These methods have the same syntax as `whereNull` and `whereNotNull` and literally
do the exact same thing.

## Parameter Grouping

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::where('name', 'Fortnite')
    ->orWhere(function($query) {
        $query->where('aggregated_rating', '>=', 90)
            ->where('aggregated_rating_count', '>=', 3000);
    })
    ->get();
```
