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
    public $ageRating;

    /**
     * @param AgeRating $ageRating
     * @return void
     */
    public function __construct(AgeRating $ageRating)
    {
        $this->ageRating = $ageRating;
    }
}
