<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\DTO\ProductBrand;

/**
 * @internal
 */
#[CoversClass(ProductBrand::class)]
final class ProductBrandTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $brand = new ProductBrand(1, 'Test Brand');
        $this->assertInstanceOf(ProductBrand::class, $brand);
        $this->assertSame(1, $brand->id);
        $this->assertSame('Test Brand', $brand->name);
    }

    public function testToArray(): void
    {
        $brand = new ProductBrand(42, 'Apple');
        $expected = [
            'id' => 42,
            'name' => 'Apple',
        ];

        $this->assertSame($expected, $brand->toArray());
    }
}
