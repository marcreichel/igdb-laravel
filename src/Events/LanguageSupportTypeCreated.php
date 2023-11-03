<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\LanguageSupportType;

class LanguageSupportTypeCreated extends Event
{
    public LanguageSupportType $data;

    public function __construct(LanguageSupportType $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
