<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Keyword;

class KeywordCreated extends Event
{
    public function __construct(public Keyword $data, Request $request)
    {
        parent::__construct($request);
    }
}
