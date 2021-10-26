<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformVersion;

class PlatformVersionCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformVersion
     */
    public PlatformVersion $data;

    /**
     * @param PlatformVersion $data
     * @param Request         $request
     */
    public function __construct(PlatformVersion $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
