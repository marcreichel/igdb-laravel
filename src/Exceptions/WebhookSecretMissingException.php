<?php

namespace MarcReichel\IGDBLaravel\Exceptions;

use Exception;

class WebhookSecretMissingException extends Exception
{
    protected $message = 'Webhook secret is missing. Please provide one in your `igdb.php` config file.';
}
