<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Enums\Image;

enum Size: string
{
    case COVER_SMALL = 'cover_small';
    case SCREENSHOT_MEDIUM = 'screenshot_med';
    case COVER_BIG = 'cover_big';
    case LOGO_MEDIUM = 'logo_med';
    case SCREENSHOT_BIG = 'screenshot_big';
    case SCREENSHOT_HUGE = 'screenshot_huge';
    case THUMBNAIL = 'thumb';
    case MICRO = 'micro';
    case HD_READY = '720p';
    case HD = '1080p';
}
