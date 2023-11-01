<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\GameVersionFeatureValue;

enum IncludedFeature: int
{
    case NOT_INCLUDED = 0;
    case INCLUDED = 1;
    case PRE_ORDER_ONLY = 2;
}
