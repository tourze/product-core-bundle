<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\ProductCoreBundle\Enum\SpuState;

/**
 * @internal
 */
#[CoversClass(SpuState::class)]
final class SpuStateTest extends AbstractEnumTestCase
{
    public function testSpecificValues(): void
    {
        $this->assertSame('1', SpuState::ONLINE->value);
        $this->assertSame('0', SpuState::OFFLINE->value);
    }

    public function testSpecificLabels(): void
    {
        $this->assertSame('上架中', SpuState::ONLINE->getLabel());
        $this->assertSame('已上架', SpuState::OFFLINE->getLabel());
    }

    public function testGenOptions(): void
    {
        $options = SpuState::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(2, $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

    public function testToArray(): void
    {
        $result = SpuState::ONLINE->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertSame('1', $result['value']);
        $this->assertSame('上架中', $result['label']);
    }
}
