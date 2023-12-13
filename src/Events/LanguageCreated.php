<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Language;

class LanguageCreated extends Event
{
    public Language $data;

    public function __construct(Language $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
