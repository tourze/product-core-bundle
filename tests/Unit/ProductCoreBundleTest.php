<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\ProductCoreBundle;

class ProductCoreBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new ProductCoreBundle();
        $this->assertInstanceOf(ProductCoreBundle::class, $bundle);
    }
}