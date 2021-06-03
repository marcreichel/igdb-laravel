<?php

namespace MarcReichel\IGDBLaravel\Exceptions;

use Exception;
use Throwable;

class InvalidWebhookUrlException extends Exception
{
    public function __construct($url)
    {
        $message = 'The provided url `' . $url . '` is not a valid url.';
        parent::__construct($message);
    }
}
