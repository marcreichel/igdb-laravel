<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\CharacterSpecie;

class CharacterSpecieCreated extends Event
{
    public function __construct(public CharacterSpecie $data, Request $request)
    {
        parent::__construct($request);
    }
}
