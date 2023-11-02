<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameEngine;

class GameEngineCreated extends Event
{
    public GameEngine $data;

    public function __construct(GameEngine $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
