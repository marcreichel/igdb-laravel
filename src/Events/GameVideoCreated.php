<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameVideo;

class GameVideoCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameVideo
     */
    public $gameVideo;

    /**
     * @param GameVideo $gameVideo
     * @return void
     */
    public function __construct(GameVideo $gameVideo)
    {
        $this->gameVideo = $gameVideo;
    }
}
