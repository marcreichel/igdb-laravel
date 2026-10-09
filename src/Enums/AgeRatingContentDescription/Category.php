<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\AgeRatingContentDescription;

/** @deprecated Use the {@see \MarcReichel\IGDBLaravel\Models\AgeRatingContentDescriptionV2} model instead. */
enum Category: int
{
    case PEGI = 1;
    case ESRB = 2;
}
