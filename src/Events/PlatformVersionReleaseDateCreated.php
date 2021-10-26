<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformVersionReleaseDate;

class PlatformVersionReleaseDateCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformVersionReleaseDate
     */
    public PlatformVersionReleaseDate $data;

    /**
     * @param PlatformVersionReleaseDate $data
     * @param Request                    $request
     */
    public function __construct(PlatformVersionReleaseDate $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
