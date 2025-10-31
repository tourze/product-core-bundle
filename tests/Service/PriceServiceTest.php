<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\Service\PriceService;

/**
 * @internal
 */
#[CoversClass(PriceService::class)]
#[RunTestsInSeparateProcesses]
final class PriceServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    public function testCanBeInstantiated(): void
    {
        $service = self::getService(PriceService::class);
        $this->assertInstanceOf(PriceService::class, $service);
    }

    public function testFindFreightPriceBySkus(): void
    {
        $service = self::getService(PriceService::class);
        $result = $service->findFreightPriceBySkus('test-freight-id', []);

        $this->assertNull($result);
    }

    public function testFindPriceById(): void
    {
        $service = self::getService(PriceService::class);
        $result = $service->findPriceById('test-price-id');

        $this->assertNull($result);
    }
}
