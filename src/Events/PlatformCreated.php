<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Platform;

class PlatformCreated extends Event
{
    public function __construct(public Platform $data, Request $request)
    {
        parent::__construct($request);
    }
}
