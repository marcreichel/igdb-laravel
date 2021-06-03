<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameVersion;

class GameVersionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameVersion
     */
    public $gameVersion;

    /**
     * @param GameVersion $gameVersion
     * @return void
     */
    public function __construct(GameVersion $gameVersion)
    {
        $this->gameVersion = $gameVersion;
    }
}
