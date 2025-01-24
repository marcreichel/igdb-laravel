<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use MarcReichel\IGDBLaravel\Enums\Image\Size;
use MarcReichel\IGDBLaravel\Models\Artwork;
use MarcReichel\IGDBLaravel\Models\CharacterMugShot;
use MarcReichel\IGDBLaravel\Models\CompanyLogo;
use MarcReichel\IGDBLaravel\Models\Cover;
use MarcReichel\IGDBLaravel\Models\GameEngineLogo;
use MarcReichel\IGDBLaravel\Models\Image;
use MarcReichel\IGDBLaravel\Models\PlatformLogo;
use MarcReichel\IGDBLaravel\Models\Screenshot;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
class ImageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Cache::put('igdb_cache.access_token', 'some-token');

        Http::fake([
            '*' => Http::response([
                [
                    'id' => 1,
                    'alpha_channel' => false,
                    'animated' => false,
                    'checksum' => 'abc',
                    'height' => 100,
                    'image_id' => 'abc',
                    'url' => '//images.igdb.com/igdb/image/upload/t_thumb/abc.jpg',
                    'width' => 100,
                ],
            ]),
        ]);
    }

    public static function imageSizeDataProvider(): array
    {
        $enumCases = collect(Size::cases())
            ->map(static fn (Size $size) => [$size, $size->value])
            ->toArray();
        $stringCases = collect(Size::cases())
            ->map(static fn (Size $size) => [$size->value, $size->value])
            ->toArray();

        return array_merge($enumCases, $stringCases);
    }

    public function testArtworkShouldBeMappedAsInstanceOfImage(): void
    {
        self::assertInstanceOf(Image::class, Artwork::first());
    }

    public function testCharacterMugShotShouldBeMappedAsInstanceOfImage(): void
    {
        self::assertInstanceOf(Image::class, CharacterMugShot::first());
    }

    public function testCompanyLogoShouldBeMappedAsInstanceOfImage(): void
    {
        self::assertInstanceOf(Image::class, CompanyLogo::first());
    }

    public function testCoverShouldBeMappedAsInstanceOfImage(): void
    {
        self::assertInstanceOf(Image::class, Cover::first());
    }

    public function testGameEngineLogoShouldBeMappedAsInstanceOfImage(): void
    {
        self::assertInstanceOf(Image::class, GameEngineLogo::first());
    }

    public function testPlatformLogoShouldBeMappedAsInstanceOfImage(): void
    {
        self::assertInstanceOf(Image::class, PlatformLogo::first());
    }

    public function testScreenshotShouldBeMappedAsInstanceOfImage(): void
    {
        self::assertInstanceOf(Image::class, Screenshot::first());
    }

    public function testItShouldGenerateDefaultImageUrlWithoutAttributes(): void
    {
        $url = Artwork::first()?->getUrl();

        self::assertEquals('//images.igdb.com/igdb/image/upload/t_thumb/abc.jpg', $url);
    }

    #[DataProvider('imageSizeDataProvider')]
    public function testItShouldGenerateDesiredImageUrlWithParameter(Size | string $size, string $value): void
    {
        $url = Artwork::first()?->getUrl($size);

        self::assertEquals('//images.igdb.com/igdb/image/upload/t_' . $value . '/abc.jpg', $url);
    }

    #[DataProvider('imageSizeDataProvider')]
    public function testItShouldGenerateRetinaImageUrl(Size | string $size, string $value): void
    {
        $url = Artwork::first()?->getUrl($size, true);

        self::assertEquals('//images.igdb.com/igdb/image/upload/t_' . $value . '_2x/abc.jpg', $url);
    }

    public function testItShouldThrowExceptionWithInvalidImageSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Artwork::first()?->getUrl('foo');
    }
}
