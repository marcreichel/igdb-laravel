<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CharacterMugShot;

class CharacterMugShotCreated extends Event
{
    public function __construct(public CharacterMugShot $data, Request $request)
    {
        parent::__construct($request);
    }
}
