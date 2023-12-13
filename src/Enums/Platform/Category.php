<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\Platform;

enum Category: int
{
    case CONSOLE = 1;
    case ARCADE = 2;
    case PLATFORM = 3;
    case OPERATING_SYSTEM = 4;
    case PORTABLE_CONSOLE = 5;
    case COMPUTER = 6;
}
