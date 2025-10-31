<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\DTO\ProductSkuDetail;

/**
 * @internal
 */
#[CoversClass(ProductSkuDetail::class)]
final class ProductSkuDetailTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $skuDetail = new ProductSkuDetail(
            id: 1,
            gtin: '1234567890123',
            title: 'Test SKU',
            valid: true,
            stock: 100,
            salesReal: 10,
            salesVirtual: 5,
            totalSales: 15,
            prices: [],
            attributes: []
        );

        $this->assertInstanceOf(ProductSkuDetail::class, $skuDetail);
        $this->assertSame(1, $skuDetail->id);
        $this->assertSame('1234567890123', $skuDetail->gtin);
        $this->assertSame('Test SKU', $skuDetail->title);
        $this->assertTrue($skuDetail->valid);
        $this->assertSame(100, $skuDetail->stock);
        $this->assertSame(10, $skuDetail->salesReal);
        $this->assertSame(5, $skuDetail->salesVirtual);
        $this->assertSame(15, $skuDetail->totalSales);
        $this->assertSame([], $skuDetail->prices);
        $this->assertSame([], $skuDetail->attributes);
    }
}
