<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameVersion;

class GameVersionCreated extends Event
{
    public GameVersion $data;

    public function __construct(GameVersion $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
