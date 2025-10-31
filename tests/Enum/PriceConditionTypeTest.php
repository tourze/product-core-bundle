<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductCoreBundle\Enum\PriceConditionType;

/**
 * @internal
 */
#[CoversClass(PriceConditionType::class)]
final class PriceConditionTypeTest extends AbstractEnumTestCase
{
    public function testGenOptions(): void
    {
        $options = PriceConditionType::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(count(PriceConditionType::cases()), $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

    public function testToArray(): void
    {
        $result = PriceConditionType::BUY_QUANTITY->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('buy-quantity', $result['value']);
        $this->assertSame('购买数量', $result['label']);
    }
}
