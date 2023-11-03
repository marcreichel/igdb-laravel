<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\EventNetwork;

class EventNetworkCreated extends Event
{
    public EventNetwork $data;

    public function __construct(EventNetwork $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
