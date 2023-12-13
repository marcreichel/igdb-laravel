<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\PlatformWebsite;

enum Category: int
{
    case OFFICIAL = 1;
    case WIKIA = 2;
    case WIKIPEDIA = 3;
    case FACEBOOK = 4;
    case TWITTER = 5;
    case TWITCH = 6;
    case INSTAGRAM = 8;
    case YOUTUBE = 9;
    case IPHONE = 10;
    case IPAD = 11;
    case ANDROID = 12;
    case STEAM = 13;
    case REDDIT = 14;
    case DISCORD = 15;
    case GOOGLE_PLUS = 16;
    case TUMBLR = 17;
    case LINKEDIN = 18;
    case PINTEREST = 19;
    case SOUNDCLOUD = 20;
}
