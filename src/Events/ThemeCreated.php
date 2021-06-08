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
    public $theme;

    /**
     * @param Theme $theme
     * @return void
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }
}
