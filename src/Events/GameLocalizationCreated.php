<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\GameLocalization;

class GameLocalizationCreated extends Event
{
    public GameLocalization $data;

    public function __construct(GameLocalization $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
