<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\ProductTypeFetcher;

/**
 * @internal
 */
#[CoversClass(ProductTypeFetcher::class)]
#[RunTestsInSeparateProcesses]
final class ProductTypeFetcherTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    public function testServiceIsAccessible(): void
    {
        $service = self::getService(ProductTypeFetcher::class);
        $this->assertInstanceOf(ProductTypeFetcher::class, $service);
    }

    public function testGenSelectData(): void
    {
        $fetcher = self::getService(ProductTypeFetcher::class);
        $this->assertInstanceOf(ProductTypeFetcher::class, $fetcher);

        $result = $fetcher->genSelectData();
        $this->assertIsIterable($result);

        $resultArray = is_array($result) ? $result : iterator_to_array($result);
        $this->assertIsArray($resultArray);
    }
}
