<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Models\Genre;

class GenreCreated extends Event
{
    public Genre $data;

    public function __construct(Genre $data, Request $request)
    {
        parent::__construct($request);
        $this->data = $data;
    }
}
