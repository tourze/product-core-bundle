<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\DTO\ProductPrice;

/**
 * @internal
 */
#[CoversClass(ProductPrice::class)]
final class ProductPriceTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $price = new ProductPrice(1, 'sale', '99.99', 'USD', 0.1, 1);
        $this->assertInstanceOf(ProductPrice::class, $price);
        $this->assertSame(1, $price->id);
        $this->assertSame('sale', $price->type);
        $this->assertSame('99.99', $price->price);
        $this->assertSame('USD', $price->currency);
        $this->assertSame(0.1, $price->taxRate);
        $this->assertSame(1, $price->priority);
    }

    public function testToArray(): void
    {
        $price = new ProductPrice(42, 'list', '149.99', 'EUR', 0.2, 2);
        $expected = [
            'id' => 42,
            'type' => 'list',
            'price' => '149.99',
            'currency' => 'EUR',
            'taxRate' => 0.2,
            'priority' => 2,
        ];

        $this->assertSame($expected, $price->toArray());
    }
}
