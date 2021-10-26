<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\Collection;

class CollectionCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Collection
     */
    public Collection $data;

    /**
     * @param Collection $data
     * @param Request    $request
     */
    public function __construct(Collection $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
