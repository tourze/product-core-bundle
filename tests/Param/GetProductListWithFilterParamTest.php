<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\ProductCoreBundle\Param\GetProductListWithFilterParam;

/**
 * @internal
 */
#[CoversClass(GetProductListWithFilterParam::class)]
final class GetProductListWithFilterParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetProductListWithFilterParam(
            minPrice: 10.0,
            keyword: 'test',
            maxPrice: 100.0,
            salesSort: 'sales_desc',
            priceSort: 'price_asc',
            categoryId: 1,
            tagId: 2,
            includeSku: true,
            pageSize: 20,
            currentPage: 1,
            lastId: 100,
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame(10.0, $param->minPrice);
        $this->assertSame('test', $param->keyword);
        $this->assertSame(100.0, $param->maxPrice);
        $this->assertSame('sales_desc', $param->salesSort);
        $this->assertSame('price_asc', $param->priceSort);
        $this->assertSame(1, $param->categoryId);
        $this->assertSame(2, $param->tagId);
        $this->assertTrue($param->includeSku);
        $this->assertSame(20, $param->pageSize);
        $this->assertSame(1, $param->currentPage);
        $this->assertSame(100, $param->lastId);
    }

    public function testParamWithDefaultValues(): void
    {
        $param = new GetProductListWithFilterParam();

        $this->assertNull($param->minPrice);
        $this->assertNull($param->keyword);
        $this->assertNull($param->maxPrice);
        $this->assertNull($param->salesSort);
        $this->assertNull($param->priceSort);
        $this->assertNull($param->categoryId);
        $this->assertNull($param->tagId);
        $this->assertTrue($param->includeSku);
        $this->assertSame(10, $param->pageSize);
        $this->assertSame(1, $param->currentPage);
        $this->assertNull($param->lastId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetProductListWithFilterParam(
            minPrice: 50.0,
            pageSize: 30,
        );

        $this->assertSame(50.0, $param->minPrice);
        $this->assertSame(30, $param->pageSize);
    }
}
