<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\DTO\ProductImage;

/**
 * @internal
 */
#[CoversClass(ProductImage::class)]
final class ProductImageTest extends TestCase
{
    public function testConstruct(): void
    {
        $image = new ProductImage('test.jpg', 1);

        $this->assertSame('test.jpg', $image->url);
        $this->assertSame(1, $image->sort);
    }

    public function testToArray(): void
    {
        $image = new ProductImage('image.png', 5);
        $array = $image->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('url', $array);
        $this->assertArrayHasKey('sort', $array);
        $this->assertSame('image.png', $array['url']);
        $this->assertSame(5, $array['sort']);
        $this->assertCount(2, $array);
    }

    public function testToArrayWithZeroSort(): void
    {
        $image = new ProductImage('zero.jpg', 0);
        $array = $image->toArray();

        $this->assertSame('zero.jpg', $array['url']);
        $this->assertSame(0, $array['sort']);
    }

    public function testToArrayWithNegativeSort(): void
    {
        $image = new ProductImage('negative.gif', -1);
        $array = $image->toArray();

        $this->assertSame('negative.gif', $array['url']);
        $this->assertSame(-1, $array['sort']);
    }
}
