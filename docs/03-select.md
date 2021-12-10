# Select (Fields)

Select which fields should be in the response. If you want to have all available fields in the response you can also
skip this method as the query builder will select `*` by default. (**Attention**: This is the opposite behaviour from
the Apicalypse API)

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::select(['*'])->get();

$games = Game::select(['name', 'first_release_date'])->get();
```
