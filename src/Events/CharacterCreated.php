<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Character;

class CharacterCreated extends Event
{
    public Character $data;

    public function __construct(Character $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
