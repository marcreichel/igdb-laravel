<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\Character;

enum Species: int
{
    case HUMAN = 1;
    case ALIEN = 2;
    case ANIMAL = 3;
    case ANDROID = 4;
    case UNKNOWN = 5;
}
