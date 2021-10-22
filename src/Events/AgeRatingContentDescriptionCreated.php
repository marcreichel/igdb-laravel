<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\AgeRatingContentDescription;

class AgeRatingContentDescriptionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var AgeRatingContentDescription
     */
    public AgeRatingContentDescription $data;

    /**
     * @param AgeRatingContentDescription $data
     *
     * @return void
     */
    public function __construct(AgeRatingContentDescription $data)
    {
        $this->data = $data;
    }
}
