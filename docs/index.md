# Introduction

{.inline-images}
[![Packagist Version](https://img.shields.io/packagist/v/marcreichel/igdb-laravel?style=for-the-badge)](https://packagist.org/packages/marcreichel/igdb-laravel)
[![Packagist Downloads](https://img.shields.io/packagist/dt/marcreichel/igdb-laravel?style=for-the-badge)](https://packagist.org/packages/marcreichel/igdb-laravel)
[![tests](https://img.shields.io/github/actions/workflow/status/marcreichel/igdb-laravel/tests.yml?event=push&style=for-the-badge&logo=github&label=tests)](https://github.com/marcreichel/igdb-laravel/actions/workflows/tests.yml)
[![Pint](https://img.shields.io/github/actions/workflow/status/marcreichel/igdb-laravel/code-style.yml?event=push&style=for-the-badge&logo=github&label=Code-Style)](https://github.com/marcreichel/igdb-laravel/actions/workflows/pint.yml)
[![PHPStan](https://img.shields.io/github/actions/workflow/status/marcreichel/igdb-laravel/code-quality.yml?event=push&style=for-the-badge&logo=github&label=Code-Quality)](https://github.com/marcreichel/igdb-laravel/actions/workflows/code-quality.yml)
[![CodeFactor](https://img.shields.io/codefactor/grade/github/marcreichel/igdb-laravel?style=for-the-badge&logo=codefactor&label=Codefactor)](https://www.codefactor.io/repository/github/marcreichel/igdb-laravel)
[![CodeCov](https://img.shields.io/codecov/c/github/marcreichel/igdb-laravel?token=m6FOB0CyPE&style=for-the-badge&logo=codecov)](https://codecov.io/gh/marcreichel/igdb-laravel)
[![GitHub](https://img.shields.io/github/license/marcreichel/igdb-laravel?style=for-the-badge)](https://packagist.org/packages/marcreichel/igdb-laravel)

![Cover](art/cover.png){style="width: 100%"}

This is a Laravel wrapper for version 4 of the [IGDB API](https://api-docs.igdb.com/) (Apicalypse) including [webhook handling](90-webhooks.md).

It handles authentication and caching of the IGDB API automatically.

## Example

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Models\Game;

$game = Game::where('name', 'Fortnite')->first();
```
