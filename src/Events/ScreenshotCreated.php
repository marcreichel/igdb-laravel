<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Screenshot;

class ScreenshotCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Screenshot
     */
    public $screenshot;

    /**
     * @param Screenshot $screenshot
     * @return void
     */
    public function __construct(Screenshot $screenshot)
    {
        $this->screenshot = $screenshot;
    }
}
