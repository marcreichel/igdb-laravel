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
    public $game;

    /**
     * @param Game $game
     * @return void
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
    }
}
