<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformLogo;

class PlatformLogoCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformLogo
     */
    public $platformLogo;

    /**
     * @param PlatformLogo $platformLogo
     * @return void
     */
    public function __construct(PlatformLogo $platformLogo)
    {
        $this->platformLogo = $platformLogo;
    }
}
