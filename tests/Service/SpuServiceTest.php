<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\Service\SpuService;

/**
 * @internal
 */
#[CoversClass(SpuService::class)]
#[RunTestsInSeparateProcesses]
final class SpuServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    public function testCanBeInstantiated(): void
    {
        $service = self::getService(SpuService::class);
        $this->assertNotNull($service);
    }

    public function testFindSpuById(): void
    {
        $service = self::getService(SpuService::class);
        $result = $service->findSpuById('test-spu-id');

        $this->assertNull($result);
    }

    public function testFindValidSpuById(): void
    {
        $service = self::getService(SpuService::class);
        $result = $service->findValidSpuById('test-spu-id');

        $this->assertNull($result);
    }

    public function testFindValidSpuByIdOrGtin(): void
    {
        $service = self::getService(SpuService::class);
        $result = $service->findValidSpuByIdOrGtin('test-value');

        $this->assertNull($result);
    }

    public function testFindAllValidSpus(): void
    {
        $service = self::getService(SpuService::class);
        $result = $service->findAllValidSpus();

        $this->assertIsIterable($result);
    }

    public function testFindSpuIdsByCatalogId(): void
    {
        $service = self::getService(SpuService::class);
        $result = $service->findSpuIdsByCatalogId('test-catalog-id');

        $this->assertIsArray($result);
    }

    public function testGetSpuIdsByCatalogDQL(): void
    {
        $service = self::getService(SpuService::class);
        $result = $service->getSpuIdsByCatalogDQL();

        $this->assertIsString($result);
    }
}
