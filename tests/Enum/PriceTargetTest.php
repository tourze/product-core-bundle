<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductCoreBundle\Enum\PriceTarget;

/**
 * @internal
 */
#[CoversClass(PriceTarget::class)]
final class PriceTargetTest extends AbstractEnumTestCase
{
    public function testGenOptions(): void
    {
        $options = PriceTarget::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(count(PriceTarget::cases()), $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

    public function testToArray(): void
    {
        $result = PriceTarget::SALE->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('sale', $result['value']);
        $this->assertSame('销售', $result['label']);
    }
}
