<?php

namespace MarcReichel\IGDBLaravel\Exceptions;

use Exception;

class InvalidWebhookSecretException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Invalid secret provided';
}
