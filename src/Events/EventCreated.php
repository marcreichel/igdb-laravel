<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Event as EventModel;

class EventCreated extends Event
{
    public function __construct(public EventModel $data, Request $request)
    {
        parent::__construct($request);
    }
}
