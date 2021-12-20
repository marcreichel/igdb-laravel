# Introduction

{.inline-images}
[![tests](https://github.com/marcreichel/igdb-laravel/actions/workflows/tests.yml/badge.svg?event=push)](https://github.com/marcreichel/igdb-laravel/actions/workflows/tests.yml)
[![PHPStan](https://github.com/marcreichel/igdb-laravel/actions/workflows/code-quality.yml/badge.svg?event=push)](https://github.com/marcreichel/igdb-laravel/actions/workflows/code-quality.yml)
[![CodeFactor](https://www.codefactor.io/repository/github/marcreichel/igdb-laravel/badge)](https://www.codefactor.io/repository/github/marcreichel/igdb-laravel)
[![Packagist Downloads](https://img.shields.io/packagist/dt/marcreichel/igdb-laravel)](https://packagist.org/packages/marcreichel/igdb-laravel)
[![Packagist Version](https://img.shields.io/packagist/v/marcreichel/igdb-laravel)](https://packagist.org/packages/marcreichel/igdb-laravel)
[![GitHub](https://img.shields.io/github/license/marcreichel/igdb-laravel)](https://packagist.org/packages/marcreichel/igdb-laravel)
[![Gitmoji](https://img.shields.io/badge/gitmoji-%20😜%20😍-FFDD67.svg)](https://gitmoji.dev)

![Cover](art/cover.png){style="width: 100%"}

This is a Laravel wrapper for version 4 of the [IGDB API](https://api-docs.igdb.com/) (Apicalypse) including [webhook handling](90-webhooks.md) since version 2.3.0.

It handles authentication and caching of the IGDB API automatically.

## Example

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::where('name', 'Fortnite')->first();
```
