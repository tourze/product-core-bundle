<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductCoreBundle\Enum\PriceConditionOperator;

/**
 * @internal
 */
#[CoversClass(PriceConditionOperator::class)]
final class PriceConditionOperatorTest extends AbstractEnumTestCase
{
    public function testGenOptions(): void
    {
        $options = PriceConditionOperator::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(count(PriceConditionOperator::cases()), $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

    public function testToArray(): void
    {
        $result = PriceConditionOperator::GTE->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('gte', $result['value']);
        $this->assertSame('大于等于', $result['label']);
    }
}
