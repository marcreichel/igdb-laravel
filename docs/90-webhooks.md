# Webhooks

Since version 2.3.0 of this package you can create webhooks and handle their requests with ease. 🎉

## Initial Setup

### Configuration

Inside your `config/igdb.php` file you need to have a `webhook_path` and `webhook_secret` of your choice like so:

```php
<?php

return [
    // ...

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

    /*
     * Base URL used for the webhook callback instead of the `APP_URL`.
     *
     * Useful when webhooks should be routed through a relay service
     * (e.g. Hookdeck) or a proxy. The webhook path is appended to it.
     */
    'webhook_base_url' => env('IGDB_WEBHOOK_BASE_URL'),
];
```

_**Please note**: You only need to add this part to your config if you have upgraded from a prior version of this
package. New installations have this configured automatically._

And then set a secret inside your `.env` file:

```dotenv
IGDB_WEBHOOK_SECRET=yoursecret
```

> Make sure your `APP_URL` (inside your `.env`) is something different than `localhost` or `127.0.0.1`. Otherwise webhooks can
> not be created.

### Custom webhook base URL

By default the webhook URL is built from your `APP_URL`. If you want IGDB to send the webhooks to a different host, e.g. a
relay service like [Hookdeck](https://hookdeck.com) or a proxy, set a base URL inside your `.env` file:

```dotenv
IGDB_WEBHOOK_BASE_URL=https://hkdk.events/your-source
```

The webhook path is appended to this base URL, so the webhook above would be created for
`https://hkdk.events/your-source/igdb-webhook/handle/{hash}/games/create`. Make sure your relay forwards this path to
your application.

That's it!

## Create a webhook

Let's say we want to be informed whenever a new game is created on https://igdb.com.

First of all we need to inform IGDB that we want to be informed.

For this we create a webhook like so (for example inside a controller):

```php
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
$ php artisan igdb:webhooks
```

### Create a webhook

```bash
$ php artisan igdb:webhooks:create {model?} {--method=}
```

You can also just call `php artisan igdb:webhooks:create` without any arguments. The command will then ask for the
required data interactively.

The `model` parameter needs to be the (studly cased) class name of a model (e.g. `Game`).

The `--method` option needs to be one of `create`, `update` or `delete` accordingly for which event you want to listen.

### Reactivate a webhook

```bash
$ php artisan igdb:webhooks:reactivate {id}
```

For `{id}` insert the id of the (inactive) webhook.

### Delete a webhook

```bash
$ php artisan igdb:webhooks:delete {id?} {--A|all}
```

You may provide the `id` of a webhook to delete it or use the `-A`/`--all` flag to delete all your registered webhooks.
