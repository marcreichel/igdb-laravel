<?php

namespace MarcReichel\IGDBLaravel\Exceptions;

use Exception;
use MarcReichel\IGDBLaravel\Enums\Webhook\Method;
use ReflectionClass;

class InvalidWebhookMethodException extends Exception
{
    public function __construct()
    {
        $reflectionClass = new ReflectionClass(Method::class);
        $allowedMethods = array_values($reflectionClass->getConstants());
        $message = 'Method must be one of ' . implode(', ', $allowedMethods);
        parent::__construct($message);
    }
}
