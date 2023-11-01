<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\Game;

enum Category: int
{
    case MAIN_GAME = 0;
    case DLC_ADDON = 1;
    case EXPANSION = 2;
    case BUNDLE = 3;
    case STANDALONE_EXPANSION = 4;
    case MOD = 5;
    case EPISODE = 6;
    case SEASON = 7;
    case REMAKE = 8;
    case REMASTER = 9;
    case EXPANDED_GAME = 10;
    case PORT = 11;
    case FORK = 12;
}
