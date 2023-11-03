<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\Webhook;

enum Method: string
{
    case CREATE = 'create';
    case DELETE = 'delete';
    case UPDATE = 'update';
}
