<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Game;

class GameCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Game
     */
    public Game $data;

    /**
     * @param Game    $data
     * @param Request $request
     */
    public function __construct(Game $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
