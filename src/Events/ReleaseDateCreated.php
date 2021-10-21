<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\ReleaseDate;

class ReleaseDateCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var ReleaseDate
     */
    public $data;

    /**
     * @param ReleaseDate $data
     *
     * @return void
     */
    public function __construct(ReleaseDate $data)
    {
        $this->data = $data;
    }
}
