<?php

namespace MarcReichel\IGDBLaravel\Models;

use Illuminate\Support\Str;
use InvalidArgumentException;
use MarcReichel\IGDBLaravel\Enums\Image\Size;
use MarcReichel\IGDBLaravel\Exceptions\PropertyDoesNotExist;
use ReflectionClass;

abstract class Image extends Model
{
    protected const IMAGE_BASE_PATH = '//images.igdb.com/igdb/image/upload';

    /**
     * @throws PropertyDoesNotExist|InvalidArgumentException
     */
    public function getUrl(string $size = 'thumb', bool $retina = false): string
    {
        $availableSizes = new ReflectionClass(Size::class);
        $constants = collect($availableSizes->getConstants());
        $sizeFromEnum = $constants->first(function ($value) use ($size) {
            return $value === $size;
        });

        if (is_null($sizeFromEnum)) {
            throw new InvalidArgumentException("[$size] is not a valid image size.");
        }

        $basePath = static::IMAGE_BASE_PATH;
        $id = $this->getAttribute('image_id');

        if (is_null($id)) {
            throw new PropertyDoesNotExist('Property [image_id] is missing from the response. Make sure you specify `image_id` inside the fields attribute.');
        }

        $id = '' . $id;

        if ($retina) {
            $size = Str::finish('' . $sizeFromEnum, '_2x');
        }

        return "$basePath/t_$size/$id.jpg";
    }
}
