# Relationships (Extends)

To extend your result use the `with`-method:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::with(['cover', 'artworks'])->get();
```

By default, every field (`*`) of the relationship is selected.
If you want to define the fields of the relationship yourself you have to define
the relationship as the array-key and the fields as an array:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::with(['cover' => ['url', 'image_id']])->get();
```
