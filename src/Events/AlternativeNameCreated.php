<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\AlternativeName;

class AlternativeNameCreated extends Event
{
    public AlternativeName $data;

    public function __construct(AlternativeName $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
