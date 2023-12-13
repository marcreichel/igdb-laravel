<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameMode;

class GameModeCreated extends Event
{
    public GameMode $data;

    public function __construct(GameMode $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
