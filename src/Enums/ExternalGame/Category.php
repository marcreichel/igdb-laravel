<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\ExternalGame;

/** @deprecated Use the {@see \MarcReichel\IGDBLaravel\Models\ExternalGameSource} model instead. */
enum Category: int
{
    case STEAM = 1;
    case GOG = 5;
    case YOUTUBE = 10;
    case MICROSOFT = 11;
    case APPLE = 13;
    case TWITCH = 14;
    case ANDROID = 15;
    case AMAZON_ASIN = 20;
    case AMAZON_LUNA = 22;
    case AMAZON_ADG = 23;
    case EPIC_GAME_STORE = 26;
    case OCULUS = 28;
    case UTOMIK = 29;
    case ITCH_IO = 30;
    case XBOX_MARKETPLACE = 31;
    case KARTRIDGE = 32;
    case PLAYSTATION_STORE_US = 36;
    case FOCUS_ENTERTAINMENT = 37;
    case XBOX_GAME_PASS_ULTIMATE_CLOUD = 54;
    case GAMEJOLT = 55;
}
