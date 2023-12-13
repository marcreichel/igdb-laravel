<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\EventLogo;

class EventLogoCreated extends Event
{
    public EventLogo $data;

    public function __construct(EventLogo $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
