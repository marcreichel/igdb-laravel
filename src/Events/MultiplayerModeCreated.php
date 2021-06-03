<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\MultiplayerMode;

class MultiplayerModeCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var MultiplayerMode
     */
    public $multiplayerMode;

    /**
     * @param MultiplayerMode $multiplayerMode
     * @return void
     */
    public function __construct(MultiplayerMode $multiplayerMode)
    {
        $this->multiplayerMode = $multiplayerMode;
    }
}
