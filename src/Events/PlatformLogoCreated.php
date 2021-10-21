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
    public $data;

    /**
     * @param PlatformLogo $data
     *
     * @return void
     */
    public function __construct(PlatformLogo $data)
    {
        $this->data = $data;
    }
}
