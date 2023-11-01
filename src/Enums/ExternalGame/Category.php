<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\ExternalGame;

enum Category: int
{
    case STEAM = 1;
    case GOG = 5;
    case YOUTUBE = 10;
    case MICROSOFT = 11;
    case APPLE = 13;
    case TWITCH = 14;
    case ANDROID = 15;
}
