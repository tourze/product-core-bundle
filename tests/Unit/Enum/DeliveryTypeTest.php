<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Enum\DeliveryType;

class DeliveryTypeTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(DeliveryType::class));
    }
}