<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\ProductCoreBundle\ProductCoreBundle;

/**
 * @internal
 */
#[CoversClass(ProductCoreBundle::class)]
#[RunTestsInSeparateProcesses]
final class ProductCoreBundleTest extends AbstractBundleTestCase
{
}
