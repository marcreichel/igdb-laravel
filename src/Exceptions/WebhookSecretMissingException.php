<?php

namespace MarcReichel\IGDBLaravel\Exceptions;

use Exception;

class WebhookSecretMissingException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Webhook secret is missing. Please provide one in your `igdb.php` config file.';
}
