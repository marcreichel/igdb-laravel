<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Language;

class LanguageCreated extends Event
{
    public function __construct(public Language $data, Request $request)
    {
        parent::__construct($request);
    }
}
