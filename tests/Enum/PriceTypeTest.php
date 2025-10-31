<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductCoreBundle\Enum\PriceType;

/**
 * @internal
 */
#[CoversClass(PriceType::class)]
final class PriceTypeTest extends AbstractEnumTestCase
{
    public function testGenOptions(): void
    {
        $options = PriceType::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(count(PriceType::cases()), $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

    public function testToArray(): void
    {
        $result = PriceType::SALE->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('sale', $result['value']);
        $this->assertSame('一口价', $result['label']);
    }
}
