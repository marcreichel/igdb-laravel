<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums;

enum TagNumbers: int
{
    case THEME = 0;
    case GENRE = 1;
    case KEYWORD = 2;
    case GAME = 3;
    case PLAYER_PERSPECTIVE = 4;
}
