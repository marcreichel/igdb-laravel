<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\EventLogo;

class EventLogoCreated extends Event
{
    public function __construct(public EventLogo $data, Request $request)
    {
        parent::__construct($request);
    }
}
