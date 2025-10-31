<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Tourze\ProductCoreBundle\DTO\ProductListFilterRequest;

/**
 * @internal
 */
#[CoversClass(ProductListFilterRequest::class)]
final class ProductListFilterRequestTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $request = new ProductListFilterRequest();

        $this->assertNull($request->minPrice);
        $this->assertNull($request->maxPrice);
        $this->assertNull($request->salesSort);
        $this->assertNull($request->priceSort);
        $this->assertNull($request->categoryId);
        $this->assertSame(1, $request->page);
        $this->assertSame(20, $request->limit);
    }

    public function testValidData(): void
    {
        $request = new ProductListFilterRequest();
        $request->minPrice = 10.0;
        $request->maxPrice = 100.0;
        $request->salesSort = 'sales_desc';
        $request->priceSort = 'price_asc';
        $request->categoryId = 1;
        $request->page = 2;
        $request->limit = 50;

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertCount(0, $violations);
    }

    public function testInvalidNegativePrice(): void
    {
        $request = new ProductListFilterRequest();
        $request->minPrice = -10.0;

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('最低价格不能小于0', (string) $violations);
    }

    public function testInvalidMaxPriceLowerThanMinPrice(): void
    {
        $request = new ProductListFilterRequest();
        $request->minPrice = 100.0;
        $request->maxPrice = 50.0;

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('最高价格必须大于最低价格', (string) $violations);
    }

    public function testInvalidSalesSort(): void
    {
        $request = new ProductListFilterRequest();
        $request->salesSort = 'invalid_sort';

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('销量排序只能是 sales_desc 或 sales_asc', (string) $violations);
    }

    public function testInvalidPriceSort(): void
    {
        $request = new ProductListFilterRequest();
        $request->priceSort = 'invalid_sort';

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('价格排序只能是 price_desc 或 price_asc', (string) $violations);
    }

    public function testInvalidCategoryId(): void
    {
        $request = new ProductListFilterRequest();
        $request->categoryId = -1;

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('分类ID必须大于0', (string) $violations);
    }

    public function testInvalidPage(): void
    {
        $request = new ProductListFilterRequest();
        $request->page = 0;

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('页码必须大于0', (string) $violations);
    }

    public function testInvalidLimitTooSmall(): void
    {
        $request = new ProductListFilterRequest();
        $request->limit = 0;

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('每页数量必须在1到100之间', (string) $violations);
    }

    public function testInvalidLimitTooLarge(): void
    {
        $request = new ProductListFilterRequest();
        $request->limit = 101;

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($request);
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString('每页数量必须在1到100之间', (string) $violations);
    }
}
