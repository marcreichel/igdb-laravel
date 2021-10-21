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
    public $data;

    /**
     * @param PlatformFamily $data
     *
     * @return void
     */
    public function __construct(PlatformFamily $data)
    {
        $this->data = $data;
    }
}
