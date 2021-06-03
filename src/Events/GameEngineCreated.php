<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameEngine;

class GameEngineCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameEngine
     */
    public $gameEngine;

    /**
     * @param GameEngine $gameEngine
     * @return void
     */
    public function __construct(GameEngine $gameEngine)
    {
        $this->gameEngine = $gameEngine;
    }
}
