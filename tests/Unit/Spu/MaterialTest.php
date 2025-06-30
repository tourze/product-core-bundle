<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Spu;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Spu\Material;

class MaterialTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(Material::class));
    }
}