<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameEngineLogo;

class GameEngineLogoCreated extends Event
{
    public GameEngineLogo $data;

    public function __construct(GameEngineLogo $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
