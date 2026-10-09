<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameStatus;

class GameStatusCreated extends Event
{
    public function __construct(public GameStatus $data, Request $request)
    {
        parent::__construct($request);
    }
}
