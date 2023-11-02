<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Artwork;

class ArtworkCreated extends Event
{
    public Artwork $data;

    public function __construct(Artwork $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
