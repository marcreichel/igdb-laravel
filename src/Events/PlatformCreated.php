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
    public $data;

    /**
     * @param Platform $data
     *
     * @return void
     */
    public function __construct(Platform $data)
    {
        $this->data = $data;
    }
}
