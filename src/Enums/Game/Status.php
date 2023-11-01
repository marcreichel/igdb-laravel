<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\Game;

enum Status: int
{
    case RELEASED = 0;
    case ALPHA = 2;
    case BETA = 3;
    case EARLY_ACCESS = 4;
    case OFFLINE = 5;
    case CANCELLED = 6;
    case RUMORED = 7;
}
