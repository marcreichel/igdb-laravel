<?php

namespace MarcReichel\IGDBLaravel\Interfaces;

interface WebhookInterface
{
    /**
     * @param  mixed  ...$parameters
     */
    public function __construct(mixed ...$parameters);
}
