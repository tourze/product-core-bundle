<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\ProductTagFetcher;

/**
 * @internal
 */
#[CoversClass(ProductTagFetcher::class)]
#[RunTestsInSeparateProcesses]
final class ProductTagFetcherTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    public function testServiceIsAccessible(): void
    {
        $service = self::getService(ProductTagFetcher::class);
        $this->assertInstanceOf(ProductTagFetcher::class, $service);
    }

    public function testGenSelectData(): void
    {
        $fetcher = self::getService(ProductTagFetcher::class);
        $this->assertInstanceOf(ProductTagFetcher::class, $fetcher);

        $result = $fetcher->genSelectData();
        $this->assertIsIterable($result);

        $resultArray = is_array($result) ? $result : iterator_to_array($result);
        $this->assertIsArray($resultArray);
    }
}
