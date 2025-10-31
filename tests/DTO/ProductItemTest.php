<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\DTO\ProductItem;

/**
 * @internal
 */
#[CoversClass(ProductItem::class)]
final class ProductItemTest extends TestCase
{
    public function testConstruct(): void
    {
        $item = new ProductItem(
            id: 1,
            name: 'Test Product',
            price: 99.99,
            originalPrice: 129.99,
            thumbnail: 'https://example.com/image.jpg',
            sales: 100,
            stock: 50
        );

        $this->assertSame(1, $item->id);
        $this->assertSame('Test Product', $item->name);
        $this->assertSame(99.99, $item->price);
        $this->assertSame(129.99, $item->originalPrice);
        $this->assertSame('https://example.com/image.jpg', $item->thumbnail);
        $this->assertSame(100, $item->sales);
        $this->assertSame(50, $item->stock);
    }

    public function testConstructWithNullValues(): void
    {
        $item = new ProductItem(
            id: 2,
            name: 'Another Product',
            price: null,
            originalPrice: null,
            thumbnail: '',
            sales: 0,
            stock: 0
        );

        $this->assertSame(2, $item->id);
        $this->assertSame('Another Product', $item->name);
        $this->assertNull($item->price);
        $this->assertNull($item->originalPrice);
        $this->assertSame('', $item->thumbnail);
        $this->assertSame(0, $item->sales);
        $this->assertSame(0, $item->stock);
    }

    public function testToArray(): void
    {
        $item = new ProductItem(
            id: 1,
            name: 'Test Product',
            price: 99.99,
            originalPrice: 129.99,
            thumbnail: 'https://example.com/image.jpg',
            sales: 100,
            stock: 50
        );

        $array = $item->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('price', $array);
        $this->assertArrayHasKey('originalPrice', $array);
        $this->assertArrayHasKey('thumbnail', $array);
        $this->assertArrayHasKey('sales', $array);
        $this->assertArrayHasKey('stock', $array);

        $this->assertSame(1, $array['id']);
        $this->assertSame('Test Product', $array['name']);
        $this->assertSame(99.99, $array['price']);
        $this->assertSame(129.99, $array['originalPrice']);
        $this->assertSame('https://example.com/image.jpg', $array['thumbnail']);
        $this->assertSame(100, $array['sales']);
        $this->assertSame(50, $array['stock']);
    }

    public function testToArrayWithNullValues(): void
    {
        $item = new ProductItem(
            id: 2,
            name: 'Another Product',
            price: null,
            originalPrice: null,
            thumbnail: '',
            sales: 0,
            stock: 0
        );

        $array = $item->toArray();

        $this->assertIsArray($array);
        $this->assertSame(2, $array['id']);
        $this->assertSame('Another Product', $array['name']);
        $this->assertNull($array['price']);
        $this->assertNull($array['originalPrice']);
        $this->assertSame('', $array['thumbnail']);
        $this->assertSame(0, $array['sales']);
        $this->assertSame(0, $array['stock']);
    }
}
