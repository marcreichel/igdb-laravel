<?php

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

    /** @test */
    public function artwork_should_be_mapped_as_instance_of_image(): void
    {
        self::assertInstanceOf(Image::class, Artwork::first());
    }

    /** @test */
    public function character_mug_shot_should_be_mapped_as_instance_of_image(): void
    {
        self::assertInstanceOf(Image::class, CharacterMugShot::first());
    }

    /** @test */
    public function company_logo_should_be_mapped_as_instance_of_image(): void
    {
        self::assertInstanceOf(Image::class, CompanyLogo::first());
    }

    /** @test */
    public function cover_should_be_mapped_as_instance_of_image(): void
    {
        self::assertInstanceOf(Image::class, Cover::first());
    }

    /** @test */
    public function game_engine_logo_should_be_mapped_as_instance_of_image(): void
    {
        self::assertInstanceOf(Image::class, GameEngineLogo::first());
    }

    /** @test */
    public function platform_logo_should_be_mapped_as_instance_of_image(): void
    {
        self::assertInstanceOf(Image::class, PlatformLogo::first());
    }

    /** @test */
    public function screenshot_should_be_mapped_as_instance_of_image(): void
    {
        self::assertInstanceOf(Image::class, Screenshot::first());
    }

    /** @test */
    public function it_should_generate_default_image_url_without_attributes(): void
    {
        $url = Artwork::first()->getUrl();

        self::assertEquals('//images.igdb.com/igdb/image/upload/t_thumb/abc.jpg', $url);
    }

    /** @test */
    public function it_should_generate_desired_image_url_with_parameter(): void
    {
        $url = Artwork::first()->getUrl(Size::COVER_BIG);

        self::assertEquals('//images.igdb.com/igdb/image/upload/t_cover_big/abc.jpg', $url);
    }

    /** @test */
    public function it_should_generate_retina_image_url(): void
    {
        $url = Artwork::first()->getUrl(Size::COVER_BIG, true);

        self::assertEquals('//images.igdb.com/igdb/image/upload/t_cover_big_2x/abc.jpg', $url);
    }

    /** @test */
    public function it_should_throw_exception_with_invalid_image_size(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Artwork::first()->getUrl('foo');
    }
}
