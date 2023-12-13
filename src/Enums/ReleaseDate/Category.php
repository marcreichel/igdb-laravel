<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\ReleaseDate;

enum Category: int
{
    case YYYYMMMMDD = 0;
    case YYYYMMMM = 1;
    case YYYY = 2;
    case YYYYQ1 = 3;
    case YYYYQ2 = 4;
    case YYYYQ3 = 5;
    case YYYYQ4 = 6;
    case TBD = 7;
}
