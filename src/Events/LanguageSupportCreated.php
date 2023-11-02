<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\LanguageSupport;

class LanguageSupportCreated extends Event
{
    public LanguageSupport $data;

    public function __construct(LanguageSupport $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
