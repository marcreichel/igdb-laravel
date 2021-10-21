<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\GameVideo;

class GameVideoCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameVideo
     */
    public $data;

    /**
     * @param GameVideo $data
     *
     * @return void
     */
    public function __construct(GameVideo $data)
    {
        $this->data = $data;
    }
}
