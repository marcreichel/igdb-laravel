<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameMode;

class GameModeCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameMode
     */
    public $gameMode;

    /**
     * @param GameMode $gameMode
     * @return void
     */
    public function __construct(GameMode $gameMode)
    {
        $this->gameMode = $gameMode;
    }
}
