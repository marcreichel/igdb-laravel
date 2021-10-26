<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameEngineLogo;

class GameEngineLogoCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameEngineLogo
     */
    public GameEngineLogo $data;

    /**
     * @param GameEngineLogo $data
     * @param Request        $request
     */
    public function __construct(GameEngineLogo $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
