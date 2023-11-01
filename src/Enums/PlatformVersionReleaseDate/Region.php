<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\PlatformVersionReleaseDate;

enum Region: int
{
    case EUROPE = 1;
    case NORTH_AMERICA = 2;
    case AUSTRALIA = 3;
    case NEW_ZEALAND = 4;
    case JAPAN = 5;
    case CHINA = 6;
    case ASIA = 7;
    case WORLDWIDE = 8;
}
