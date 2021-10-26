<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\MultiplayerMode;

class MultiplayerModeCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var MultiplayerMode
     */
    public MultiplayerMode $data;

    /**
     * @param MultiplayerMode $data
     * @param Request         $request
     */
    public function __construct(MultiplayerMode $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
