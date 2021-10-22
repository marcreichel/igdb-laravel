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
    public GameMode $data;

    /**
     * @param GameMode $data
     *
     * @return void
     */
    public function __construct(GameMode $data)
    {
        $this->data = $data;
    }
}
