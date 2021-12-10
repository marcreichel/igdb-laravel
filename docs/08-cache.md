# Cache

You can overwrite the default cache time for one specific query. So you can for
example turn off caching for a query:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::cache(0)->get();
```
