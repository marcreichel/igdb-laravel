<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformWebsite;

class PlatformWebsiteCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformWebsite
     */
    public $platformWebsite;

    /**
     * @param PlatformWebsite $platformWebsite
     * @return void
     */
    public function __construct(PlatformWebsite $platformWebsite)
    {
        $this->platformWebsite = $platformWebsite;
    }
}
