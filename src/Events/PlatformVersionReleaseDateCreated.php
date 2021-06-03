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
    public $platformVersionReleaseDate;

    /**
     * @param PlatformVersionReleaseDate $platformVersionReleaseDate
     * @return void
     */
    public function __construct(PlatformVersionReleaseDate $platformVersionReleaseDate)
    {
        $this->platformVersionReleaseDate = $platformVersionReleaseDate;
    }
}
