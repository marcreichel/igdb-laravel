<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Screenshot;

class ScreenshotCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Screenshot
     */
    public Screenshot $data;

    /**
     * @param Screenshot $data
     * @param Request    $request
     */
    public function __construct(Screenshot $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
