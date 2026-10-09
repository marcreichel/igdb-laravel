<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\ExternalGame;

/** @deprecated Use the {@see \MarcReichel\IGDBLaravel\Models\GameReleaseFormat} model instead. */
enum Media: int
{
    case DIGITAL = 1;
    case PHYSICAL = 2;
}
