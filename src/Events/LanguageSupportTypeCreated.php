<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\LanguageSupportType;

class LanguageSupportTypeCreated extends Event
{
    public function __construct(public LanguageSupportType $data, Request $request)
    {
        parent::__construct($request);
    }
}
