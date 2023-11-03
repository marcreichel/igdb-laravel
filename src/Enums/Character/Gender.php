<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\Character;

enum Gender: int
{
    case MALE = 0;
    case FEMALE = 1;
    case OTHER = 2;
}
