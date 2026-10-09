<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\AgeRating;

/** @deprecated Use the {@see \MarcReichel\IGDBLaravel\Models\AgeRatingOrganization} model instead. */
enum Category: int
{
    case ESRB = 1;
    case PEGI = 2;
    case CERO = 3;
    case USK = 4;
    case GRAC = 5;
    case CLASS_IND = 6;
    case ACB = 7;
}
