<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\ReleaseDate;

class ReleaseDateCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var ReleaseDate
     */
    public ReleaseDate $data;

    /**
     * @param ReleaseDate $data
     * @param Request     $request
     */
    public function __construct(ReleaseDate $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
