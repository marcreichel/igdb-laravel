<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\AlternativeName;

class AlternativeNameCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var AlternativeName
     */
    public AlternativeName $data;

    /**
     * @param AlternativeName $data
     * @param Request         $request
     */
    public function __construct(AlternativeName $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
