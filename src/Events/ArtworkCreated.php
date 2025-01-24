<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Artwork;

class ArtworkCreated extends Event
{
    public function __construct(public Artwork $data, Request $request)
    {
        parent::__construct($request);
    }
}
