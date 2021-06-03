<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlayerPerspective;

class PlayerPerspectiveCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlayerPerspective
     */
    public $playerPerspective;

    /**
     * @param PlayerPerspective $playerPerspective
     * @return void
     */
    public function __construct(PlayerPerspective $playerPerspective)
    {
        $this->playerPerspective = $playerPerspective;
    }
}
