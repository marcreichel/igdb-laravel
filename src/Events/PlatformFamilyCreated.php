<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformFamily;

class PlatformFamilyCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformFamily
     */
    public PlatformFamily $data;

    /**
     * @param PlatformFamily $data
     * @param Request        $request
     */
    public function __construct(PlatformFamily $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
