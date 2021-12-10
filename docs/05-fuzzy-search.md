# Fuzzy Search

The fuzzy search (since v3.1.0) acts like a "where like" chain under the hood.

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$games = Game::fuzzySearch(
    // fields to search in
    [
        'name',
        'involved_companies.company.name', // you can search for nested values as well
    ],
    // the query to search for
    'Call of Duty',
    // enable/disable case sensitivity (disabled by default)
    false,
)->get();
```

**Attention**: Keep in mind you have to do the sorting of the results yourself. They are not ordered by relevance or
any other way.
