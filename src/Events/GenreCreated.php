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
    public $genre;

    /**
     * @param Genre $genre
     * @return void
     */
    public function __construct(Genre $genre)
    {
        $this->genre = $genre;
    }
}
