<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameEngine;

class GameEngineCreated extends Event
{
    public function __construct(public GameEngine $data, Request $request)
    {
        parent::__construct($request);
    }
}
