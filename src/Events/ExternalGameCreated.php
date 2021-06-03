<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\ExternalGame;

class ExternalGameCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var ExternalGame
     */
    public $externalGame;

    /**
     * @param ExternalGame $externalGame
     * @return void
     */
    public function __construct(ExternalGame $externalGame)
    {
        $this->externalGame = $externalGame;
    }
}
