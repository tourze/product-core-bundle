<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Repository\SkuRepository;
use Tourze\ProductCoreBundle\Service\SkuService;

/**
 * @internal
 */
#[CoversClass(SkuService::class)]
#[RunTestsInSeparateProcesses]
final class SkuServiceTest extends AbstractIntegrationTestCase
{
    private SkuService $service;

    private SkuRepository $repository;

    protected function onSetUp(): void
    {
        $this->service = self::getService(SkuService::class);
        $this->repository = self::getService(SkuRepository::class);
        $this->cleanupTestData();
    }

    protected function onTearDown(): void
    {
        $this->cleanupTestData();
    }

    public function testCanBeInstantiated(): void
    {
        $service = self::getService(SkuService::class);
        $this->assertNotNull($service);
    }

    public function testGetAllSkus(): void
    {
        // 创建测试数据
        $spu = new Spu();
        $spu->setTitle('Test SPU for GetAllSkus');

        $sku1 = new Sku();
        $sku1->setSpu($spu);
        $sku1->setUnit('个');
        $sku1->setGtin('TEST-SKU-1');

        $sku2 = new Sku();
        $sku2->setSpu($spu);
        $sku2->setUnit('个');
        $sku2->setGtin('TEST-SKU-2');

        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->persist($sku1);
        $entityManager->persist($sku2);
        $entityManager->flush();

        $allSkus = $this->service->getAllSkus();

        $this->assertIsArray($allSkus);
        $this->assertGreaterThanOrEqual(2, count($allSkus));

        // 验证我们创建的SKU在结果中
        $foundTestSkus = 0;
        foreach ($allSkus as $sku) {
            if (in_array($sku->getGtin(), ['TEST-SKU-1', 'TEST-SKU-2'], true)) {
                ++$foundTestSkus;
            }
        }
        $this->assertEquals(2, $foundTestSkus);
    }

    public function testFindById(): void
    {
        // 创建测试数据
        $spu = new Spu();
        $spu->setTitle('Test SPU for FindById');

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个');
        $sku->setGtin('TEST-SKU-FIND-BY-ID');

        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->persist($sku);
        $entityManager->flush();

        $skuId = $sku->getId();

        // 测试查找存在的SKU
        $foundSku = $this->service->findById($skuId);
        $this->assertNotNull($foundSku);
        $this->assertInstanceOf(Sku::class, $foundSku);
        $this->assertEquals($sku->getId(), $foundSku->getId());
        $this->assertEquals('TEST-SKU-FIND-BY-ID', $foundSku->getGtin());

        // 测试查找不存在的SKU
        $notFoundSku = $this->service->findById('999999');
        $this->assertNull($notFoundSku);
    }

    public function testIncreaseSalesReal(): void
    {
        // 创建测试数据
        $spu = new Spu();
        $spu->setTitle('Test SPU for IncreaseSalesReal');

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个');
        $sku->setGtin('TEST-SKU-SALES');
        $sku->setSalesReal(10); // 初始销量

        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->persist($sku);
        $entityManager->flush();

        $skuId = $sku->getId();

        // 增加销量
        $this->service->increaseSalesReal($skuId, 5);

        // 重新获取实体以验证更新
        $entityManager->clear();
        $updatedSku = $this->repository->find($skuId);

        $this->assertNotNull($updatedSku);
        $this->assertEquals(15, $updatedSku->getSalesReal());
    }

    public function testIncreaseSalesRealWithZeroQuantity(): void
    {
        // 创建测试数据
        $spu = new Spu();
        $spu->setTitle('Test SPU for IncreaseSalesReal Zero');

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个');
        $sku->setGtin('TEST-SKU-SALES-ZERO');
        $sku->setSalesReal(20);

        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->persist($sku);
        $entityManager->flush();

        $skuId = $sku->getId();
        $originalSales = $sku->getSalesReal();

        // 增加0个销量
        $this->service->increaseSalesReal($skuId, 0);

        // 重新获取实体以验证没有变化
        $entityManager->clear();
        $updatedSku = $this->repository->find($skuId);

        $this->assertNotNull($updatedSku);
        $this->assertEquals($originalSales, $updatedSku->getSalesReal());
    }

    public function testIncreaseSalesRealWithNegativeQuantity(): void
    {
        // 创建测试数据
        $spu = new Spu();
        $spu->setTitle('Test SPU for IncreaseSalesReal Negative');

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个');
        $sku->setGtin('TEST-SKU-SALES-NEGATIVE');
        $sku->setSalesReal(30);

        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->persist($sku);
        $entityManager->flush();

        $skuId = $sku->getId();

        // 增加负数销量（实际上是减少）
        $this->service->increaseSalesReal($skuId, -10);

        // 重新获取实体以验证更新
        $entityManager->clear();
        $updatedSku = $this->repository->find($skuId);

        $this->assertNotNull($updatedSku);
        $this->assertEquals(20, $updatedSku->getSalesReal());
    }

    public function testFindByIds(): void
    {
        // 创建测试数据
        $spu = new Spu();
        $spu->setTitle('Test SPU for FindByIds');

        $sku1 = new Sku();
        $sku1->setSpu($spu);
        $sku1->setUnit('个');
        $sku1->setGtin('TEST-SKU-FIND-BY-IDS-1');

        $sku2 = new Sku();
        $sku2->setSpu($spu);
        $sku2->setUnit('个');
        $sku2->setGtin('TEST-SKU-FIND-BY-IDS-2');

        $sku3 = new Sku();
        $sku3->setSpu($spu);
        $sku3->setUnit('个');
        $sku3->setGtin('TEST-SKU-FIND-BY-IDS-3');

        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->persist($sku1);
        $entityManager->persist($sku2);
        $entityManager->persist($sku3);
        $entityManager->flush();

        // 测试根据ID列表查找
        $ids = [$sku1->getId(), $sku2->getId()];
        $foundSkus = $this->service->findByIds($ids);

        $this->assertIsArray($foundSkus);
        $this->assertCount(2, $foundSkus);

        $foundIds = array_map(fn ($sku) => $sku->getId(), $foundSkus);
        $this->assertContains($sku1->getId(), $foundIds);
        $this->assertContains($sku2->getId(), $foundIds);

        // 测试空ID列表
        $emptyResult = $this->service->findByIds([]);
        $this->assertIsArray($emptyResult);
        $this->assertEmpty($emptyResult);

        // 测试不存在的ID
        $nonExistentResult = $this->service->findByIds(['999999']);
        $this->assertIsArray($nonExistentResult);
        $this->assertEmpty($nonExistentResult);

        // 测试部分存在、部分不存在的ID
        $mixedIds = [$sku1->getId(), '999999', $sku3->getId()];
        $mixedResult = $this->service->findByIds($mixedIds);

        $this->assertIsArray($mixedResult);
        $this->assertCount(2, $mixedResult);

        $mixedFoundIds = array_map(fn ($sku) => $sku->getId(), $mixedResult);
        $this->assertContains($sku1->getId(), $mixedFoundIds);
        $this->assertContains($sku3->getId(), $mixedFoundIds);
    }

    public function testFindByGtin(): void
    {
        // 创建测试数据
        $spu = new Spu();
        $spu->setTitle('Test SPU for FindByGtin');

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个');
        $sku->setGtin('TEST-SKU-FIND-BY-GTIN');

        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->persist($sku);
        $entityManager->flush();

        // 测试查找存在的SKU
        $foundSku = $this->service->findByGtin('TEST-SKU-FIND-BY-GTIN');
        $this->assertNotNull($foundSku);
        $this->assertInstanceOf(Sku::class, $foundSku);
        $this->assertEquals('TEST-SKU-FIND-BY-GTIN', $foundSku->getGtin());

        // 测试查找不存在的GTIN
        $notFoundSku = $this->service->findByGtin('NON-EXISTENT-GTIN');
        $this->assertNull($notFoundSku);
    }

    public function testFindByGtins(): void
    {
        // 创建测试数据
        $spu = new Spu();
        $spu->setTitle('Test SPU for FindByGtins');

        $sku1 = new Sku();
        $sku1->setSpu($spu);
        $sku1->setUnit('个');
        $sku1->setGtin('TEST-SKU-GTINS-1');

        $sku2 = new Sku();
        $sku2->setSpu($spu);
        $sku2->setUnit('个');
        $sku2->setGtin('TEST-SKU-GTINS-2');

        $sku3 = new Sku();
        $sku3->setSpu($spu);
        $sku3->setUnit('个');
        $sku3->setGtin('TEST-SKU-GTINS-3');

        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->persist($sku1);
        $entityManager->persist($sku2);
        $entityManager->persist($sku3);
        $entityManager->flush();

        // 测试根据GTIN列表查找
        $gtins = ['TEST-SKU-GTINS-1', 'TEST-SKU-GTINS-2'];
        $foundSkus = $this->service->findByGtins($gtins);

        $this->assertIsArray($foundSkus);
        $this->assertCount(2, $foundSkus);

        $foundGtins = array_map(fn ($sku) => $sku->getGtin(), $foundSkus);
        $this->assertContains('TEST-SKU-GTINS-1', $foundGtins);
        $this->assertContains('TEST-SKU-GTINS-2', $foundGtins);

        // 测试空GTIN列表
        $emptyResult = $this->service->findByGtins([]);
        $this->assertIsArray($emptyResult);
        $this->assertEmpty($emptyResult);

        // 测试不存在的GTIN
        $nonExistentResult = $this->service->findByGtins(['NON-EXISTENT-GTIN']);
        $this->assertIsArray($nonExistentResult);
        $this->assertEmpty($nonExistentResult);

        // 测试部分存在、部分不存在的GTIN
        $mixedGtins = ['TEST-SKU-GTINS-1', 'NON-EXISTENT-GTIN', 'TEST-SKU-GTINS-3'];
        $mixedResult = $this->service->findByGtins($mixedGtins);

        $this->assertIsArray($mixedResult);
        $this->assertCount(2, $mixedResult);

        $mixedFoundGtins = array_map(fn ($sku) => $sku->getGtin(), $mixedResult);
        $this->assertContains('TEST-SKU-GTINS-1', $mixedFoundGtins);
        $this->assertContains('TEST-SKU-GTINS-3', $mixedFoundGtins);
    }

    private function cleanupTestData(): void
    {
        try {
            $entityManager = self::getEntityManager();

            if (!$entityManager->isOpen() || !$entityManager->getConnection()->isConnected()) {
                return;
            }

            // 清理测试创建的SKU
            $testSkus = $entityManager->createQuery(
                'SELECT s FROM Tourze\ProductCoreBundle\Entity\Sku s WHERE s.gtin LIKE :pattern'
            )
                ->setParameter('pattern', 'TEST-SKU%')
                ->getResult()
            ;

            if (is_iterable($testSkus)) {
                foreach ($testSkus as $sku) {
                    $this->assertInstanceOf(Sku::class, $sku);
                    $entityManager->remove($sku);
                }
            }

            // 清理测试创建的SPU
            $testSpus = $entityManager->createQuery(
                'SELECT s FROM Tourze\ProductCoreBundle\Entity\Spu s WHERE s.title LIKE :pattern'
            )
                ->setParameter('pattern', '%Test SPU for%')
                ->getResult()
            ;

            if (is_iterable($testSpus)) {
                foreach ($testSpus as $spu) {
                    $this->assertInstanceOf(Spu::class, $spu);
                    $entityManager->remove($spu);
                }
            }

            $entityManager->flush();
        } catch (\Exception $e) {
            // 忽略数据库清理失败的情况
        }
    }
}
