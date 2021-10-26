<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameMode;

class GameModeCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameMode
     */
    public GameMode $data;

    /**
     * @param GameMode $data
     * @param Request  $request
     */
    public function __construct(GameMode $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
