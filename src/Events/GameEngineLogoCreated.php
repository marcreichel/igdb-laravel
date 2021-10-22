<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameEngineLogo;

class GameEngineLogoCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameEngineLogo
     */
    public GameEngineLogo $data;

    /**
     * @param GameEngineLogo $data
     *
     * @return void
     */
    public function __construct(GameEngineLogo $data)
    {
        $this->data = $data;
    }
}
