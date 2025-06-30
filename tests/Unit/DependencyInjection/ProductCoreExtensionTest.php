<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\DependencyInjection\ProductCoreExtension;

class ProductCoreExtensionTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(ProductCoreExtension::class));
    }
}