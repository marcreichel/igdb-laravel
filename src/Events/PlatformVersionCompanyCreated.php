<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformVersionCompany;

class PlatformVersionCompanyCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformVersionCompany
     */
    public $platformVersionCompany;

    /**
     * @param PlatformVersionCompany $platformVersionCompany
     * @return void
     */
    public function __construct(PlatformVersionCompany $platformVersionCompany)
    {
        $this->platformVersionCompany = $platformVersionCompany;
    }
}
