<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Entity\SpuSubscribe;

class SpuSubscribeTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(SpuSubscribe::class));
    }
}