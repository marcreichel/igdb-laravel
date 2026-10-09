<h1 align="center">Laravel IGDB Wrapper</h1>

<p align="center">
    This is a Laravel wrapper for version 4 of the <a href="https://api-docs.igdb.com/">IGDB API</a> (Apicalypse)
    including <a href="docs/90-webhooks.md">webhook handling</a>.
</p>

<p align="center">
    <a href="https://packagist.org/packages/marcreichel/igdb-laravel">
        <img src="https://img.shields.io/packagist/v/marcreichel/igdb-laravel?style=for-the-badge" alt="Packagist Version">
    </a>
    <a href="https://packagist.org/packages/marcreichel/igdb-laravel">
        <img src="https://img.shields.io/packagist/dt/marcreichel/igdb-laravel?style=for-the-badge" alt="Packagist Downloads">
    </a>
    <a href="https://github.com/marcreichel/igdb-laravel/actions/workflows/tests.yml">
        <img src="https://img.shields.io/github/actions/workflow/status/marcreichel/igdb-laravel/tests.yml?event=push&style=for-the-badge&logo=github&label=tests" alt="Tests">
    </a>
    <a href="https://github.com/marcreichel/igdb-laravel/actions/workflows/pint.yml">
        <img src="https://img.shields.io/github/actions/workflow/status/marcreichel/igdb-laravel/code-style.yml?event=push&style=for-the-badge&logo=github&label=Code-Style" alt="Pint">
    </a>
    <a href="https://github.com/marcreichel/igdb-laravel/actions/workflows/code-quality.yml">
        <img src="https://img.shields.io/github/actions/workflow/status/marcreichel/igdb-laravel/code-quality.yml?event=push&style=for-the-badge&logo=github&label=Code-Quality" alt="PHPStan">
    </a>
    <a href="https://www.codefactor.io/repository/github/marcreichel/igdb-laravel">
        <img src="https://img.shields.io/codefactor/grade/github/marcreichel/igdb-laravel?style=for-the-badge&logo=codefactor&label=Codefactor" alt="CodeFactor">
    </a>
    <a href="https://codecov.io/gh/marcreichel/igdb-laravel">
        <img src="https://img.shields.io/codecov/c/github/marcreichel/igdb-laravel?token=m6FOB0CyPE&style=for-the-badge&logo=codecov" alt="codecov">
    </a>
    <a href="https://packagist.org/packages/marcreichel/igdb-laravel">
        <img src="https://img.shields.io/github/license/marcreichel/igdb-laravel?style=for-the-badge" alt="License">
    </a>
</p>

![Cover](docs/art/cover.png)

## Installation

[Create](https://dev.twitch.tv/console/apps/create) a Twitch Developer App, then install the package via composer:

```bash
composer require marcreichel/igdb-laravel
```

Publish the config file to `config/igdb.php` and set your `TWITCH_CLIENT_ID` and `TWITCH_CLIENT_SECRET`:

```bash
php artisan igdb:publish
```

## Example

```php
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::where('name', 'Fortnite')->first();
```

## Documentation

1. [Installation](docs/01-installation.md)
2. [Getting started](docs/02-getting-started.md)
3. [Select (Fields)](docs/03-select.md)
4. [Search](docs/04-search.md)
5. [Fuzzy Search](docs/05-fuzzy-search.md)
6. [Where clauses](docs/06-where-clauses.md)
7. [Ordering, Limit, & Offset](docs/07-order-limit-offset.md)
8. [Cache](docs/08-cache.md)
9. [Relationships (Extends)](docs/09-relationships.md)
10. [Fetch results](docs/10-fetch-results.md)
11. [Reading properties](docs/11-properties.md)
12. [Images](docs/12-images.md)
13. [Webhooks](docs/90-webhooks.md)

## Testing

Run the tests with:

```bash
composer test
```

## Contribution

Pull requests are welcome :)
