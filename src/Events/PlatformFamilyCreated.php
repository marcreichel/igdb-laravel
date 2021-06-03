<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\PlatformFamily;

class PlatformFamilyCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlatformFamily
     */
    public $platformFamily;

    /**
     * @param PlatformFamily $platformFamily
     * @return void
     */
    public function __construct(PlatformFamily $platformFamily)
    {
        $this->platformFamily = $platformFamily;
    }
}
