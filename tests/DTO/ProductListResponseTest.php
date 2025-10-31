<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\DTO\ProductItem;
use Tourze\ProductCoreBundle\DTO\ProductListResponse;

/**
 * @internal
 */
#[CoversClass(ProductListResponse::class)]
final class ProductListResponseTest extends TestCase
{
    public function testConstruct(): void
    {
        $item1 = new ProductItem(1, 'Product 1', 99.99, 129.99, 'thumb1.jpg', 100, 50);
        $item2 = new ProductItem(2, 'Product 2', 79.99, 99.99, 'thumb2.jpg', 80, 30);

        $response = new ProductListResponse(
            items: [$item1, $item2],
            total: 150,
            page: 1,
            limit: 20,
            totalPages: 8
        );

        $this->assertCount(2, $response->items);
        $this->assertSame(150, $response->total);
        $this->assertSame(1, $response->page);
        $this->assertSame(20, $response->limit);
        $this->assertSame(8, $response->totalPages);
    }

    public function testConstructWithEmptyItems(): void
    {
        $response = new ProductListResponse(
            items: [],
            total: 0,
            page: 1,
            limit: 20,
            totalPages: 0
        );

        $this->assertCount(0, $response->items);
        $this->assertSame(0, $response->total);
        $this->assertSame(1, $response->page);
        $this->assertSame(20, $response->limit);
        $this->assertSame(0, $response->totalPages);
    }

    public function testToArray(): void
    {
        $item1 = new ProductItem(1, 'Product 1', 99.99, 129.99, 'thumb1.jpg', 100, 50);
        $item2 = new ProductItem(2, 'Product 2', 79.99, 99.99, 'thumb2.jpg', 80, 30);

        $response = new ProductListResponse(
            items: [$item1, $item2],
            total: 150,
            page: 1,
            limit: 20,
            totalPages: 8
        );

        $array = $response->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('items', $array);
        $this->assertArrayHasKey('pagination', $array);

        $items = $array['items'];
        $this->assertIsArray($items);
        $this->assertCount(2, $items);

        $this->assertIsArray($items[0] ?? null);
        $this->assertIsArray($items[1] ?? null);

        $pagination = $array['pagination'];
        $this->assertIsArray($pagination);
        $this->assertArrayHasKey('total', $pagination);
        $this->assertArrayHasKey('page', $pagination);
        $this->assertArrayHasKey('limit', $pagination);
        $this->assertArrayHasKey('totalPages', $pagination);

        $this->assertSame(150, $pagination['total']);
        $this->assertSame(1, $pagination['page']);
        $this->assertSame(20, $pagination['limit']);
        $this->assertSame(8, $pagination['totalPages']);
    }

    public function testToArrayWithEmptyItems(): void
    {
        $response = new ProductListResponse(
            items: [],
            total: 0,
            page: 1,
            limit: 20,
            totalPages: 0
        );

        $array = $response->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('items', $array);
        $this->assertArrayHasKey('pagination', $array);

        $items = $array['items'];
        $this->assertIsArray($items);
        $this->assertCount(0, $items);

        $pagination = $array['pagination'];
        $this->assertIsArray($pagination);
        $this->assertSame(0, $pagination['total']);
        $this->assertSame(1, $pagination['page']);
        $this->assertSame(20, $pagination['limit']);
        $this->assertSame(0, $pagination['totalPages']);
    }

    public function testItemsContainCorrectData(): void
    {
        $item = new ProductItem(
            id: 123,
            name: 'Test Product',
            price: 49.99,
            originalPrice: 59.99,
            thumbnail: 'test-thumb.jpg',
            sales: 200,
            stock: 15
        );

        $response = new ProductListResponse(
            items: [$item],
            total: 1,
            page: 1,
            limit: 20,
            totalPages: 1
        );

        $array = $response->toArray();
        $items = $array['items'];
        $this->assertIsArray($items);
        $this->assertArrayHasKey(0, $items);
        $itemArray = $items[0];
        $this->assertIsArray($itemArray);

        $this->assertSame(123, $itemArray['id']);
        $this->assertSame('Test Product', $itemArray['name']);
        $this->assertSame(49.99, $itemArray['price']);
        $this->assertSame(59.99, $itemArray['originalPrice']);
        $this->assertSame('test-thumb.jpg', $itemArray['thumbnail']);
        $this->assertSame(200, $itemArray['sales']);
        $this->assertSame(15, $itemArray['stock']);
    }
}
