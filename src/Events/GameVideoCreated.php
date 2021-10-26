<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameVideo;

class GameVideoCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameVideo
     */
    public GameVideo $data;

    /**
     * @param GameVideo $data
     * @param Request   $request
     */
    public function __construct(GameVideo $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
