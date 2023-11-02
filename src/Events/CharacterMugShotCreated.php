<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CharacterMugShot;

class CharacterMugShotCreated extends Event
{
    public CharacterMugShot $data;

    public function __construct(CharacterMugShot $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
