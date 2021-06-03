<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Platform;

class PlatformCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Platform
     */
    public $platform;

    /**
     * @param Platform $platform
     * @return void
     */
    public function __construct(Platform $platform)
    {
        $this->platform = $platform;
    }
}
