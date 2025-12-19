<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
use Tourze\ProductCoreBundle\Param\GetProductListWithFilterParam;
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

    public function testParamDefaultValues(): void
    {
        $param = new GetProductListWithFilterParam();

        $this->assertNull($param->minPrice);
        $this->assertNull($param->maxPrice);
        $this->assertNull($param->salesSort);
        $this->assertNull($param->priceSort);
        $this->assertNull($param->categoryId);
        $this->assertNull($param->tagId);
        $this->assertTrue($param->includeSku);
        $this->assertSame(10, $param->pageSize);
        $this->assertSame(1, $param->currentPage);
    }

    public function testParamAssignment(): void
    {
        $param = new GetProductListWithFilterParam(
            minPrice: 10.0,
            maxPrice: 100.0,
            salesSort: 'sales_desc',
            priceSort: 'price_asc',
            categoryId: 123,
            tagId: 456,
            includeSku: false,
            pageSize: 20,
            currentPage: 2
        );

        $this->assertSame(10.0, $param->minPrice);
        $this->assertSame(100.0, $param->maxPrice);
        $this->assertSame('sales_desc', $param->salesSort);
        $this->assertSame('price_asc', $param->priceSort);
        $this->assertSame(123, $param->categoryId);
        $this->assertSame(456, $param->tagId);
        $this->assertFalse($param->includeSku);
        $this->assertSame(20, $param->pageSize);
        $this->assertSame(2, $param->currentPage);
    }

    public function testIncludeSkuDefaultValue(): void
    {
        $param = new GetProductListWithFilterParam();

        // 默认情况下，includeSku 应该为 true
        $this->assertTrue($param->includeSku);
    }

    public function testIncludeSkuParameterControl(): void
    {
        // 测试 includeSku 参数可以被正确设置
        $param = new GetProductListWithFilterParam(includeSku: false);
        $this->assertFalse($param->includeSku);

        $param = new GetProductListWithFilterParam(includeSku: true);
        $this->assertTrue($param->includeSku);
    }

    public function testSalesSortParameter(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试销量排序参数
        $param = new GetProductListWithFilterParam(
            salesSort: 'sales_desc',
            pageSize: 10,
            currentPage: 1
        );

        $result = $procedure->execute($param);
        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Result\ArrayResult::class, $result);

        $data = $result->data;
        $this->assertIsArray($data);
        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);
    }

    public function testPriceSortParameter(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试价格排序参数 - 升序（应使用MIN价格）
        $param = new GetProductListWithFilterParam(
            priceSort: 'price_asc',
            pageSize: 10,
            currentPage: 1
        );

        $result = $procedure->execute($param);
        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Result\ArrayResult::class, $result);

        $data = $result->data;
        $this->assertIsArray($data);
        $this->assertArrayHasKey('list', $data);

        // 测试价格排序参数 - 降序（应使用MAX价格）
        $param = new GetProductListWithFilterParam(
            priceSort: 'price_desc',
            pageSize: 10,
            currentPage: 1
        );

        $result = $procedure->execute($param);
        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Result\ArrayResult::class, $result);

        $data = $result->data;
        $this->assertIsArray($data);
        $this->assertArrayHasKey('list', $data);
    }

    public function testCombinedSortParameters(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试同时设置销量和价格排序
        $param = new GetProductListWithFilterParam(
            salesSort: 'sales_desc',
            priceSort: 'price_asc',
            pageSize: 5,
            currentPage: 1
        );

        $result = $procedure->execute($param);
        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Result\ArrayResult::class, $result);

        $data = $result->data;
        $this->assertIsArray($data);
        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);
    }

    public function testExecute(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试基本执行
        $param = new GetProductListWithFilterParam(
            pageSize: 10,
            currentPage: 1
        );

        $result = $procedure->execute($param);
        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Result\ArrayResult::class, $result);

        $data = $result->data;
        $this->assertIsArray($data);
        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);
    }

    public function testExecuteWithKeyword(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试关键词搜索
        $param = new GetProductListWithFilterParam(
            keyword: 'test',
            pageSize: 10,
            currentPage: 1
        );

        $result = $procedure->execute($param);
        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Result\ArrayResult::class, $result);

        $data = $result->data;
        $this->assertIsArray($data);
        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);
    }

    public function testExecuteWithPriceFilter(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试价格过滤
        $param = new GetProductListWithFilterParam(
            minPrice: 10.0,
            maxPrice: 100.0,
            pageSize: 10,
            currentPage: 1
        );

        $result = $procedure->execute($param);
        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Result\ArrayResult::class, $result);

        $data = $result->data;
        $this->assertIsArray($data);
        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);
    }

    public function testExecuteWithCategoryFilter(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试分类过滤
        $param = new GetProductListWithFilterParam(
            categoryId: 123,
            pageSize: 10,
            currentPage: 1
        );

        $result = $procedure->execute($param);
        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Result\ArrayResult::class, $result);

        $data = $result->data;
        $this->assertIsArray($data);
        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);
    }

    public function testExecuteWithTagFilter(): void
    {
        $procedure = self::getContainer()->get(GetProductListWithFilter::class);
        $this->assertInstanceOf(GetProductListWithFilter::class, $procedure);

        // 测试标签过滤
        $param = new GetProductListWithFilterParam(
            tagId: 456,
            pageSize: 10,
            currentPage: 1
        );

        $result = $procedure->execute($param);
        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Result\ArrayResult::class, $result);

        $data = $result->data;
        $this->assertIsArray($data);
        $this->assertArrayHasKey('list', $data);
        $this->assertArrayHasKey('pagination', $data);
    }
}
