# Webhooks

Since version 2.3.0 of this package you can create webhooks and handle their requests with ease. 🎉

## Initial Setup

### Configuration

Inside your `config/igdb.php` file you need to have a `webhook_path` and `webhook_secret` of your choice like so:

```php
// torchlight! {"lineNumbers": false}
// torchlight! {"summaryCollapsedIndicator": "⌄"}
<?php

return [
    // [tl! collapse:start]
    /*
     * These are the credentials you got from https://dev.twitch.tv/console/apps [tl! autolink]
     */
    'credentials' => [
        'client_id' => env('TWITCH_CLIENT_ID', ''),
        'client_secret' => env('TWITCH_CLIENT_SECRET', ''),
    ],

    /*
     * This package caches queries automatically (for 1 hour per default).
     * Here you can set how long each query should be cached (in seconds).
     *
     * To turn cache off set this value to 0
     */
    'cache_lifetime' => env('IGDB_CACHE_LIFETIME', 3600), // [tl! collapse:end]

    /*
     * Path where the webhooks should be handled.
     */
    'webhook_path' => 'igdb-webhook/handle',

    /*
     * The webhook secret.
     *
     * This needs to be a string of your choice in order to use the webhook
     * functionality.
     */
    'webhook_secret' => env('IGDB_WEBHOOK_SECRET'),
];
```

_**Please note**: You only need to add this part to your config if you have upgraded from a prior version of this
package. New installations have this configured automatically._

And then set a secret inside your `.env` file:

```dotenv
// torchlight! {"lineNumbers": false}
IGDB_WEBHOOK_SECRET=yoursecret
```

> Make sure your `APP_URL` (inside your `.env`) is something different than `localhost` or `127.0.0.1`. Otherwise webhooks can
> not be created.

That's it!

## Create a webhook

Let's say we want to be informed whenever a new game is created on https://igdb.com.

First of all we need to inform IGDB that we want to be informed.

For this we create a webhook like so (for example inside a controller):

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use MarcReichel\IGDBLaravel\Models\Game;
use Illuminate\Routing\Controller;

class ExampleController extends Controller
{
    public function createWebhook()
    {
        Game::createWebhook(Method::CREATE)
    }
}
```

## Listen for events

Now that we have created our webhook we can listen for a specific event - in our case when a game is created.

For this we create a Laravel EventListener or for sake of simplicity we just listen for an event inside the `boot()`
method of our `app/providers/EventServiceProvider.php`:

```php
// torchlight! {"lineNumbers": false}
use MarcReichel\IGDBLaravel\Events\GameCreated;
use Illuminate\Support\Facades\Event;

public function boot()
{
    Event::listen(function (GameCreated $event) {
        // $event->data holds the (unexpanded!) data (of the game in this case)
    });
}
```

[Here](https://github.com/marcreichel/igdb-laravel/tree/main/src/Events) you can find a list of all available events.

Further information on how to set up event listeners can be found on
the [official docs](https://laravel.com/docs/events).

## Manage webhooks via CLI

### List your webhooks

```bash
// torchlight! {"lineNumbers": false}
$ php artisan igdb:webhooks
```

### Create a webhook

```bash
// torchlight! {"lineNumbers": false}
$ php artisan igdb:webhooks:create {model?} {--method=}
```

You can also just call `php artisan igdb:webhooks:create` without any arguments. The command will then ask for the
required data interactively.

The `model` parameter needs to be the (studly cased) class name of a model (e.g. `Game`).

The `--method` option needs to be one of `create`, `update` or `delete` accordingly for which event you want to listen.

### Reactivate a webhook

```bash
// torchlight! {"lineNumbers": false}
$ php artisan igdb:webhooks:reactivate {id}
```

For `{id}` insert the id of the (inactive) webhook.

### Delete a webhook

```bash
// torchlight! {"lineNumbers": false}
$ php artisan igdb:webhooks:delete {id?} {--A|all}
```

You may provide the `id` of a webhook to delete it or use the `-A`/`--all` flag to delete all your registered webhooks.
