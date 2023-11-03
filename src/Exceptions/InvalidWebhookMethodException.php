<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Exceptions;

use Exception;

class InvalidWebhookMethodException extends Exception
{
    public function __construct()
    {
        $message = 'Method must be one of ' . implode(', ', ['create', 'update', 'delete']);
        parent::__construct($message);
    }
}
