<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductCoreBundle\Enum\SpuType;

/**
 * @internal
 */
#[CoversClass(SpuType::class)]
final class SpuTypeTest extends AbstractEnumTestCase
{
    public function testEnumCases(): void
    {
        $cases = SpuType::cases();
        $this->assertIsArray($cases);
        $this->assertContains(SpuType::NORMAL, $cases);
        $this->assertContains(SpuType::PACKAGE, $cases);
    }

    public function testGenOptions(): void
    {
        $options = SpuType::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(count(SpuType::cases()), $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

    public function testToArray(): void
    {
        $result = SpuType::NORMAL->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('normal', $result['value']);
        $this->assertSame('普通商品', $result['label']);
    }
}
