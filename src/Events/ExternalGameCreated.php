<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\ExternalGame;

class ExternalGameCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var ExternalGame
     */
    public ExternalGame $data;

    /**
     * @param ExternalGame $data
     * @param Request      $request
     */
    public function __construct(ExternalGame $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
