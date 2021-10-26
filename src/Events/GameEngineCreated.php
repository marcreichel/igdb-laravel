<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameEngine;

class GameEngineCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameEngine
     */
    public GameEngine $data;

    /**
     * @param GameEngine $data
     * @param Request    $request
     */
    public function __construct(GameEngine $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
