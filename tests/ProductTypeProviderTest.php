<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\ProductTypeProvider;

/**
 * @internal
 */
#[CoversClass(ProductTypeProvider::class)]
#[RunTestsInSeparateProcesses]
final class ProductTypeProviderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    public function testServiceIsAccessible(): void
    {
        $service = self::getService(ProductTypeProvider::class);
        $this->assertInstanceOf(ProductTypeProvider::class, $service);
    }

    public function testGenSelectData(): void
    {
        $provider = self::getService(ProductTypeProvider::class);
        $this->assertInstanceOf(ProductTypeProvider::class, $provider);

        $result = $provider->genSelectData();
        $this->assertIsIterable($result);

        $resultArray = is_array($result) ? $result : iterator_to_array($result);
        $this->assertIsArray($resultArray);
        $this->assertNotEmpty($resultArray);

        // 验证第一个元素的结构
        $firstItem = $resultArray[0];
        $this->assertIsArray($firstItem);
        $this->assertArrayHasKey('label', $firstItem);
        $this->assertArrayHasKey('text', $firstItem);
        $this->assertArrayHasKey('value', $firstItem);
        $this->assertArrayHasKey('name', $firstItem);
        $this->assertEquals('normal', $firstItem['value']);
    }
}
