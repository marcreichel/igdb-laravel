<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Game;

class GameCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Game
     */
    public $data;

    /**
     * @param Game $data
     *
     * @return void
     */
    public function __construct(Game $data)
    {
        $this->data = $data;
    }
}
