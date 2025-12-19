<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\ProductCoreBundle;

/**
 * Product Core Bundle 具体测试类
 * @internal
 */
#[CoversClass(ProductCoreBundle::class)]
final class ProductCoreTest extends TestCase
{
    public function testProductCoreBundleIsLoaded(): void
    {
        $bundle = new ProductCoreBundle();
        $this->assertInstanceOf(ProductCoreBundle::class, $bundle);
    }
}
