<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductCoreBundle\Enum\DeliveryType;

/**
 * @internal
 */
#[CoversClass(DeliveryType::class)]
final class DeliveryTypeTest extends AbstractEnumTestCase
{
    public function testGenOptions(): void
    {
        $options = DeliveryType::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(count(DeliveryType::cases()), $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

    public function testToArray(): void
    {
        $result = DeliveryType::EXPRESS->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('express', $result['value']);
        $this->assertSame('快递配送', $result['label']);
    }
}
