<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use MarcReichel\IGDBLaravel\Enums\Image\Size;
use MarcReichel\IGDBLaravel\Models\Artwork;
use MarcReichel\IGDBLaravel\Models\CharacterMugShot;
use MarcReichel\IGDBLaravel\Models\CompanyLogo;
use MarcReichel\IGDBLaravel\Models\Cover;
use MarcReichel\IGDBLaravel\Models\GameEngineLogo;
use MarcReichel\IGDBLaravel\Models\Image;
use MarcReichel\IGDBLaravel\Models\PlatformLogo;
use MarcReichel\IGDBLaravel\Models\Screenshot;

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
        $url = Artwork::first()->getUrl();

        self::assertEquals('//images.igdb.com/igdb/image/upload/t_thumb/abc.jpg', $url);
    }

    public function testItShouldGenerateDesiredImageUrlWithParameter(): void
    {
        $url = Artwork::first()->getUrl(Size::COVER_BIG);

        self::assertEquals('//images.igdb.com/igdb/image/upload/t_cover_big/abc.jpg', $url);
    }

    public function testItShouldGenerateRetinaImageUrl(): void
    {
        $url = Artwork::first()->getUrl(Size::COVER_BIG, true);

        self::assertEquals('//images.igdb.com/igdb/image/upload/t_cover_big_2x/abc.jpg', $url);
    }
}
