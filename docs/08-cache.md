# Cache

You can overwrite the default cache time for one specific query. So you can for
example turn off caching for a query:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::cache(0)->get();
```

## Retries

Failed requests are attempted 3 times in total by default (`igdb.retries`). If
the access token got rejected, a new one is requested before the next attempt.
You can overwrite the number of attempts for one specific query:

```php
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::retries(5)->get();
```
