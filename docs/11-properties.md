# Reading properties

## Model-based approach

If you used the Model-based approach you can simply get a property:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::find(1905);

if ($game) {
    echo $game->name; // Will output "Fortnite"
}
```

If you want to access a property which does not exist `null` is returned:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::find(1905);

if ($game) {
    echo $game->foo; // Will output nothing
}
```

## Query Builder-based approach

If you used the Query Builder itself you must check if a property exists
yourself.
