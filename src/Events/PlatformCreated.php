<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Platform;

class PlatformCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Platform
     */
    public Platform $data;

    /**
     * @param Platform $data
     * @param Request  $request
     */
    public function __construct(Platform $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
