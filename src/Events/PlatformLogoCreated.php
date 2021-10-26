<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformLogo;

class PlatformLogoCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformLogo
     */
    public PlatformLogo $data;

    /**
     * @param PlatformLogo $data
     * @param Request      $request
     */
    public function __construct(PlatformLogo $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
