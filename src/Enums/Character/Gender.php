<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\Character;

/** @deprecated Use the {@see \MarcReichel\IGDBLaravel\Models\CharacterGender} model instead. */
enum Gender: int
{
    case MALE = 0;
    case FEMALE = 1;
    case OTHER = 2;
}
