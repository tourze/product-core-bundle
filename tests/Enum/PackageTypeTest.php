<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductCoreBundle\Enum\PackageType;

/**
 * @internal
 */
#[CoversClass(PackageType::class)]
final class PackageTypeTest extends AbstractEnumTestCase
{
    public function testGenOptions(): void
    {
        $options = PackageType::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(count(PackageType::cases()), $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

    public function testToArray(): void
    {
        $result = PackageType::COUPON->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('coupon', $result['value']);
        $this->assertSame('优惠券', $result['label']);
    }
}
