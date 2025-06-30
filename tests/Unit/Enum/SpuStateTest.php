<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Enum\SpuState;

class SpuStateTest extends TestCase
{
    public function testValues(): void
    {
        $this->assertSame('1', SpuState::ONLINE->value);
        $this->assertSame('0', SpuState::OFFLINE->value);
    }

    public function testLabels(): void
    {
        $this->assertSame('上架中', SpuState::ONLINE->getLabel());
        $this->assertSame('已上架', SpuState::OFFLINE->getLabel());
    }

    public function testSelectableInterface(): void
    {
        $this->assertContains('Tourze\EnumExtra\Selectable', class_implements(SpuState::class));
    }

    public function testItemableInterface(): void
    {
        $this->assertContains('Tourze\EnumExtra\Itemable', class_implements(SpuState::class));
    }
}