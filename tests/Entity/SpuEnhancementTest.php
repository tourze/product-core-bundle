<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductCoreBundle\Entity\Spu;

/**
 * @internal
 */
#[CoversClass(Spu::class)]
final class SpuEnhancementTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Spu();
    }

    /**
     * 提供基本属性用于 getter/setter 测试
     */
    /**
     * @return array<int, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            ['title', 'Test Product Title'],
            ['gtin', 'TEST123456'],
            ['subtitle', 'Test Subtitle'],
            ['content', 'Test content description'],
            ['remark', 'Test remark'],
            ['valid', true],
        ];
    }

    public function testGetImagesWithEmptyThumbs(): void
    {
        $spu = new Spu();

        $this->assertEmpty($spu->getImages());

        $spu->setThumbs(null);
        $this->assertEmpty($spu->getImages());

        $spu->setThumbs([]);
        $this->assertEmpty($spu->getImages());
    }

    public function testGetImagesWithArrayThumbs(): void
    {
        $spu = new Spu();

        $thumbs = [
            ['url' => 'image1.jpg', 'sort' => 2],
            ['url' => 'image2.jpg', 'sort' => 1],
            ['url' => 'image3.jpg'],
        ];

        $spu->setThumbs($thumbs);
        $images = $spu->getImages();

        $this->assertCount(3, $images);

        $this->assertSame('image2.jpg', $images[0]['url']);
        $this->assertSame(1, $images[0]['sort']);

        $this->assertSame('image1.jpg', $images[1]['url']);
        $this->assertSame(2, $images[1]['sort']);

        $this->assertSame('image3.jpg', $images[2]['url']);
        $this->assertSame(2, $images[2]['sort']);
    }

    public function testGetImagesWithStringThumbs(): void
    {
        $spu = new Spu();

        $thumbs = [
            'image1.jpg',
            'image2.jpg',
            'image3.jpg',
        ];

        $spu->setThumbs($thumbs);
        $images = $spu->getImages();

        $this->assertCount(3, $images);

        $this->assertSame('image1.jpg', $images[0]['url']);
        $this->assertSame(0, $images[0]['sort']);

        $this->assertSame('image2.jpg', $images[1]['url']);
        $this->assertSame(1, $images[1]['sort']);

        $this->assertSame('image3.jpg', $images[2]['url']);
        $this->assertSame(2, $images[2]['sort']);
    }

    public function testGetImagesWithMixedThumbs(): void
    {
        $spu = new Spu();

        $thumbs = [
            'string_image.jpg',
            ['url' => 'array_image.jpg', 'sort' => 5],
            ['url' => 'no_sort.jpg'],
            123,
            ['invalid' => 'data'],
        ];

        $spu->setThumbs($thumbs);
        $images = $spu->getImages();

        $this->assertCount(3, $images);

        $this->assertSame('string_image.jpg', $images[0]['url']);
        $this->assertSame(0, $images[0]['sort']);

        $this->assertSame('no_sort.jpg', $images[1]['url']);
        $this->assertSame(2, $images[1]['sort']);

        $this->assertSame('array_image.jpg', $images[2]['url']);
        $this->assertSame(5, $images[2]['sort']);
    }

    public function testSetImages(): void
    {
        $spu = new Spu();

        $images = [
            ['url' => 'test1.jpg', 'sort' => 1],
            ['url' => 'test2.jpg', 'sort' => 2],
        ];

        $spu->setImages($images);

        $this->assertSame($images, $spu->getThumbs());

        $retrievedImages = $spu->getImages();
        $this->assertCount(2, $retrievedImages);
        $this->assertSame('test1.jpg', $retrievedImages[0]['url']);
        $this->assertSame(1, $retrievedImages[0]['sort']);
    }

    public function testImagesSortingOrder(): void
    {
        $spu = new Spu();

        $thumbs = [
            ['url' => 'c.jpg', 'sort' => 30],
            ['url' => 'a.jpg', 'sort' => 10],
            ['url' => 'b.jpg', 'sort' => 20],
        ];

        $spu->setThumbs($thumbs);
        $images = $spu->getImages();

        $this->assertSame('a.jpg', $images[0]['url']);
        $this->assertSame('b.jpg', $images[1]['url']);
        $this->assertSame('c.jpg', $images[2]['url']);
    }

    public function testGetImagesReturnsCorrectArrayStructure(): void
    {
        $spu = new Spu();

        $thumbs = [
            ['url' => 'test.jpg', 'sort' => 1, 'extra' => 'ignored'],
        ];

        $spu->setThumbs($thumbs);
        $images = $spu->getImages();

        $this->assertCount(1, $images);
        $this->assertArrayHasKey('url', $images[0]);
        $this->assertArrayHasKey('sort', $images[0]);
        $this->assertArrayNotHasKey('extra', $images[0]);
        $this->assertCount(2, $images[0]);
    }
}
