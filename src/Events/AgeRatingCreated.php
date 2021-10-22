<?php

namespace MarcReichel\IGDBLaravel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarcReichel\IGDBLaravel\Models\AgeRating;

class AgeRatingCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var AgeRating
     */
    public AgeRating $data;

    /**
     * @param AgeRating $data
     *
     * @return void
     */
    public function __construct(AgeRating $data)
    {
        $this->data = $data;
    }
}
