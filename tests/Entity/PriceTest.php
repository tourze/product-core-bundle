<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductCoreBundle\Entity\Price;

/**
 * @internal
 */
#[CoversClass(Price::class)]
final class PriceTest extends AbstractEntityTestCase
{
    protected function createEntity(): Price
    {
        return new Price();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'price' => ['price', '99.99'],
        ];
    }

    public function testCanBeInstantiated(): void
    {
        $price = new Price();
        $this->assertInstanceOf(Price::class, $price);
    }

    public function testSetAndGetPrice(): void
    {
        $price = new Price();
        $priceValue = '99.99';

        $price->setPrice($priceValue);
        $this->assertSame($priceValue, $price->getPrice());
    }
}
