<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformVersionReleaseDate;

class PlatformVersionReleaseDateCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformVersionReleaseDate
     */
    public PlatformVersionReleaseDate $data;

    /**
     * @param PlatformVersionReleaseDate $data
     *
     * @return void
     */
    public function __construct(PlatformVersionReleaseDate $data)
    {
        $this->data = $data;
    }
}
