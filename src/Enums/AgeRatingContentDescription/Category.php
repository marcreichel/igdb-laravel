<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\AgeRatingContentDescription;

enum Category: int
{
    case PEGI = 1;
    case ESRB = 2;
}
