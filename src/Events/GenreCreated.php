<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Genre;

class GenreCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Genre
     */
    public Genre $data;

    /**
     * @param Genre   $data
     * @param Request $request
     */
    public function __construct(Genre $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
