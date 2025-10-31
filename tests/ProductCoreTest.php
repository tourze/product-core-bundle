<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\ProductCoreBundle\ProductCoreBundle;

/**
 * Product Core Bundle 具体测试类
 * @internal
 */
#[CoversClass(ProductCoreBundle::class)]
class ProductCoreTest extends ProductCoreBaseTestCase
{
    public function testProductCoreBundleIsLoaded(): void
    {
        $bundle = new ProductCoreBundle();
        $this->assertInstanceOf(ProductCoreBundle::class, $bundle);
    }
}
