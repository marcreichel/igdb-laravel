<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformWebsite;

class PlatformWebsiteCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformWebsite
     */
    public PlatformWebsite $data;

    /**
     * @param PlatformWebsite $data
     * @param Request         $request
     */
    public function __construct(PlatformWebsite $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
