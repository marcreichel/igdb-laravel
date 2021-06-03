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
    public $ageRatingContentDescription;

    /**
     * @param AgeRatingContentDescription $ageRatingContentDescription
     * @return void
     */
    public function __construct(AgeRatingContentDescription $ageRatingContentDescription)
    {
        $this->ageRatingContentDescription = $ageRatingContentDescription;
    }
}
