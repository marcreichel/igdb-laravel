<?php

namespace MarcReichel\IGDBLaravel\Exceptions;

use Exception;

class InvalidWebhookSecretException extends Exception
{
    protected $message = 'Invalid secret provided';
}
