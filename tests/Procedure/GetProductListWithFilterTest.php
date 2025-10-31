<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\ProductCoreBundle\Procedure\GetProductListWithFilter;

/**
 * @internal
 */
#[CoversClass(GetProductListWithFilter::class)]
#[RunTestsInSeparateProcesses]
final class GetProductListWithFilterTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testCanBeInstantiated(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);
    }

    public function testPublicProperties(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        $this->assertNull($procedure->minPrice);
        $this->assertNull($procedure->maxPrice);
        $this->assertNull($procedure->salesSort);
        $this->assertNull($procedure->priceSort);
        $this->assertNull($procedure->categoryId);
        $this->assertNull($procedure->tagId);
        $this->assertTrue($procedure->includeSku);
    }

    public function testPropertyAssignment(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        $procedure->minPrice = 10.0;
        $procedure->maxPrice = 100.0;
        $procedure->salesSort = 'sales_desc';
        $procedure->priceSort = 'price_asc';
        $procedure->categoryId = 123;
        $procedure->tagId = 456;
        $procedure->includeSku = false;

        $this->assertSame(10.0, $procedure->minPrice);
        $this->assertSame(100.0, $procedure->maxPrice);
        $this->assertSame('sales_desc', $procedure->salesSort);
        $this->assertSame('price_asc', $procedure->priceSort);
        $this->assertSame(123, $procedure->categoryId);
        $this->assertSame(456, $procedure->tagId);
        $this->assertFalse($procedure->includeSku);
    }

    public function testIncludeSkuDefaultValue(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 默认情况下，includeSku 应该为 true
        $this->assertTrue($procedure->includeSku);
    }

    public function testIncludeSkuParameterControl(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试 includeSku 参数可以被正确设置
        $procedure->includeSku = false;
        $this->assertFalse($procedure->includeSku);

        $procedure->includeSku = true;
        $this->assertTrue($procedure->includeSku);
    }

    public function testSalesSortParameter(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试销量排序参数
        $procedure->salesSort = 'sales_desc';
        $procedure->pageSize = 10;
        $procedure->currentPage = 1;

        $result = $procedure->execute();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('pagination', $result);
    }

    public function testPriceSortParameter(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试价格排序参数 - 升序（应使用MIN价格）
        $procedure->priceSort = 'price_asc';
        $procedure->pageSize = 10;
        $procedure->currentPage = 1;

        $result = $procedure->execute();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('list', $result);

        // 测试价格排序参数 - 降序（应使用MAX价格）
        $procedure->priceSort = 'price_desc';

        $result = $procedure->execute();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('list', $result);
    }

    public function testCombinedSortParameters(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试同时设置销量和价格排序
        $procedure->salesSort = 'sales_desc';
        $procedure->priceSort = 'price_asc';
        $procedure->pageSize = 5;
        $procedure->currentPage = 1;

        $result = $procedure->execute();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('pagination', $result);
    }

    public function testExecute(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试基本执行
        $procedure->pageSize = 10;
        $procedure->currentPage = 1;

        $result = $procedure->execute();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('pagination', $result);
    }
}
