<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\DTO\ProductCategory;

/**
 * @internal
 */
#[CoversClass(ProductCategory::class)]
final class ProductCategoryTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $category = new ProductCategory(1, 'Electronics', true);
        $this->assertInstanceOf(ProductCategory::class, $category);
        $this->assertSame(1, $category->id);
        $this->assertSame('Electronics', $category->name);
        $this->assertTrue($category->valid);
    }

    public function testToArray(): void
    {
        $category = new ProductCategory(42, 'Computers', false);
        $expected = [
            'id' => 42,
            'name' => 'Computers',
            'valid' => false,
        ];

        $this->assertSame($expected, $category->toArray());
    }
}
