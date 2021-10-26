<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Theme;

class ThemeCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Theme
     */
    public Theme $data;

    /**
     * @param Theme   $data
     * @param Request $request
     */
    public function __construct(Theme $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
