<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Interfaces;

interface WebhookInterface
{
    public function __construct(mixed ...$parameters);
}
