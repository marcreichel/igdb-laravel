<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Artwork;

class ArtworkCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Artwork
     */
    public Artwork $data;

    /**
     * @param Artwork $data
     * @param Request $request
     */
    public function __construct(Artwork $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
