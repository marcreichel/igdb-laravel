<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\PlatformVersionReleaseDate;

/** @deprecated Use the {@see \MarcReichel\IGDBLaravel\Models\ReleaseDateRegion} model instead. */
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
    case KOREA = 9;
    case BRAZIL = 10;
}
