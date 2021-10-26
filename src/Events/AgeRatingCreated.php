<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\AgeRating;

class AgeRatingCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var AgeRating
     */
    public AgeRating $data;

    /**
     * @param AgeRating $data
     * @param Request   $request
     */
    public function __construct(AgeRating $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
