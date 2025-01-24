<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\AlternativeName;

class AlternativeNameCreated extends Event
{
    public function __construct(public AlternativeName $data, Request $request)
    {
        parent::__construct($request);
    }
}
