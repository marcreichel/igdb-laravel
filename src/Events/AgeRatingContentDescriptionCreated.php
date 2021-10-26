<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\AgeRatingContentDescription;

class AgeRatingContentDescriptionCreated extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var AgeRatingContentDescription
     */
    public AgeRatingContentDescription $data;

    /**
     * @param AgeRatingContentDescription $data
     * @param Request                     $request
     */
    public function __construct(AgeRatingContentDescription $data, Request $request)
    {
        $this->data = $data;
        parent::__construct($request);
    }
}
