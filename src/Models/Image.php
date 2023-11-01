<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Models;

use Illuminate\Support\Str;
use InvalidArgumentException;
use MarcReichel\IGDBLaravel\Enums\Image\Size;
use MarcReichel\IGDBLaravel\Exceptions\PropertyDoesNotExist;

abstract class Image extends Model
{
    protected const IMAGE_BASE_PATH = '//images.igdb.com/igdb/image/upload';

    /**
     * @throws PropertyDoesNotExist|InvalidArgumentException
     */
    public function getUrl(Size $size = Size::THUMBNAIL, bool $retina = false): string
    {
        $basePath = static::IMAGE_BASE_PATH;
        $id = $this->getAttribute('image_id');

        if ($id === null) {
            throw new PropertyDoesNotExist('Property [image_id] is missing from the response. Make sure you specify `image_id` inside the fields attribute.');
        }

        $id = '' . $id;

        $parsedSize = $size->value;

        if ($retina) {
            $parsedSize = Str::finish($parsedSize, '_2x');
        }

        return "$basePath/t_$parsedSize/$id.jpg";
    }
}
