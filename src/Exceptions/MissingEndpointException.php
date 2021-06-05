<?php

namespace MarcReichel\IGDBLaravel\Exceptions;

use Exception;

class MissingEndpointException extends Exception
{
    protected $message = 'Please provide an endpoint.';
}
