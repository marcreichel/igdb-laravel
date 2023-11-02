<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Event as EventModel;

class EventCreated extends Event
{
    public EventModel $data;

    public function __construct(EventModel $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
