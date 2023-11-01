<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Event as EventModel;

class EventCreated extends Event
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public EventModel $data;

    public function __construct(EventModel $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
