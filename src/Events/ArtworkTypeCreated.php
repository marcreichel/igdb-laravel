<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\ArtworkType;

class ArtworkTypeCreated extends Event
{
    public function __construct(public ArtworkType $data, Request $request)
    {
        parent::__construct($request);
    }
}
