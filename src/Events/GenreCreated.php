<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Genre;

class GenreCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Genre
     */
    public Genre $data;

    /**
     * @param Genre $data
     *
     * @return void
     */
    public function __construct(Genre $data)
    {
        $this->data = $data;
    }
}
