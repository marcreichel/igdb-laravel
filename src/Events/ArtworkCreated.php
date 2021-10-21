<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Artwork;

class ArtworkCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Artwork
     */
    public $data;

    /**
     * @param Artwork $data
     *
     * @return void
     */
    public function __construct(Artwork $data)
    {
        $this->data = $data;
    }
}
