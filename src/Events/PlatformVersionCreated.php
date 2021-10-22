<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformVersion;

class PlatformVersionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformVersion
     */
    public PlatformVersion $data;

    /**
     * @param PlatformVersion $data
     *
     * @return void
     */
    public function __construct(PlatformVersion $data)
    {
        $this->data = $data;
    }
}
