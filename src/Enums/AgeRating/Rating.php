<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\AgeRating;

enum Rating: int
{
    case THREE = 1;
    case SEVEN = 2;
    case TWELVE = 3;
    case SIXTEEN = 4;
    case EIGHTEEN = 5;
    case RP = 6;
    case EC = 7;
    case E = 8;
    case E10 = 9;
    case T = 10;
    case M = 11;
    case AO = 12;
}
