<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\AgeRating;

/** @deprecated Use the {@see \MarcReichel\IGDBLaravel\Models\AgeRatingCategory} model instead. */
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
    case CERO_A = 13;
    case CERO_B = 14;
    case CERO_C = 15;
    case CERO_D = 16;
    case CERO_Z = 17;
    case USK_0 = 18;
    case USK_6 = 19;
    case USK_12 = 20;
    case USK_16 = 21;
    case USK_18 = 22;
    case GRAC_ALL = 23;
    case GRAC_TWELVE = 24;
    case GRAC_FIFTEEN = 25;
    case GRAC_EIGHTEEN = 26;
    case GRAC_TESTING = 27;
    case CLASS_IND_L = 28;
    case CLASS_IND_TEN = 29;
    case CLASS_IND_TWELVE = 30;
    case CLASS_IND_FOURTEEN = 31;
    case CLASS_IND_SIXTEEN = 32;
    case CLASS_IND_EIGHTEEN = 33;
    case ACB_G = 34;
    case ACB_PG = 35;
    case ACB_M = 36;
    case ACB_MA15 = 37;
    case ACB_R18 = 38;
    case ACB_RC = 39;
}
