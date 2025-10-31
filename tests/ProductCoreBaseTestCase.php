<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Product Core Bundle 基础测试类
 * @internal
 */
#[CoversClass(self::class)]
abstract class ProductCoreBaseTestCase extends TestCase
{
    /**
     * 获取Bundle的根路径
     */
    protected function getBundleRootPath(): string
    {
        return dirname(__DIR__);
    }

    protected function onSetUp(): void
    {
        // 子类可以重写此方法进行特定的设置
    }
}
