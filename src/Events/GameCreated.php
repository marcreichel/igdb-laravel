<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Game;

class GameCreated extends Event
{
    public function __construct(public Game $data, Request $request)
    {
        parent::__construct($request);
    }
}
