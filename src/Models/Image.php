<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

use Illuminate\Support\Str;
use InvalidArgumentException;
use MarcReichel\IGDBLaravel\Enums\Image\Size;

abstract class Image extends Model
{
    protected const IMAGE_BASE_PATH = '//images.igdb.com/igdb/image/upload';

    /**
     * @throws InvalidArgumentException
     */
    public function getUrl(Size | string $size = Size::THUMBNAIL, bool $retina = false): string
    {
        $basePath = static::IMAGE_BASE_PATH;
        $id = $this->getAttribute('image_id');

        if ($size instanceof Size) {
            $parsedSize = $size->value;
        } else {
            $parsedSize = $size;
        }

        $cases = collect(Size::cases())
            ->map(static fn (Size $s) => $s->value)
            ->values()
            ->toArray();

        if (!in_array($parsedSize, $cases, true)) {
            throw new InvalidArgumentException('Size must be one of ' . implode(', ', $cases));
        }

        if ($retina) {
            $parsedSize = Str::finish($parsedSize, '_2x');
        }

        return "$basePath/t_$parsedSize/$id.jpg";
    }
}
