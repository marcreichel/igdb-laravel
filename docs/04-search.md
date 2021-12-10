# Search

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::search('Fortnite')->get();
```

## Searchable models

- `Character`
- `Collection`
- `Game`
- `Platform`
- `Theme`
