<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Theme;

class ThemeCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Theme
     */
    public $data;

    /**
     * @param Theme $data
     *
     * @return void
     */
    public function __construct(Theme $data)
    {
        $this->data = $data;
    }
}
