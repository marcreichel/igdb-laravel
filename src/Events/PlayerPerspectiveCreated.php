<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\PlayerPerspective;

class PlayerPerspectiveCreated extends Event
{
    public PlayerPerspective $data;

    public function __construct(PlayerPerspective $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
