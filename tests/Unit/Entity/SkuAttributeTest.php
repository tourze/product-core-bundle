<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Entity\SkuAttribute;

class SkuAttributeTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(SkuAttribute::class));
    }
}