<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformVersionCompany;

class PlatformVersionCompanyCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformVersionCompany
     */
    public PlatformVersionCompany $data;

    /**
     * @param PlatformVersionCompany $data
     * @param Request                $request
     */
    public function __construct(PlatformVersionCompany $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
