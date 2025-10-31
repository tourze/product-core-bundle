<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\CatalogBundle\Entity\CatalogType;
use Tourze\CatalogBundle\Repository\CatalogRepository;
use Tourze\CatalogBundle\Repository\CatalogTypeRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Enum\SpuState;
use Tourze\ProductCoreBundle\Repository\SpuRepository;

/**
 * 测试 SPU 和 Catalog 之间的关系集成测试
 *
 * 该测试类通过集成测试方式验证 SpuRepository 在处理 SPU-Catalog 关系时的行为。
 * 也包括了 SpuRepository 的所有公共方法的集成测试。
 *
 * @internal
 */
#[CoversClass(SpuRepository::class)]
#[RunTestsInSeparateProcesses]
final class SpuCatalogRelationIntegrationTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // No additional setup needed for this test
    }

    public function testSpuCatalogRelationCreation(): void
    {
        self::cleanDatabase();
        $em = self::getEntityManager();

        // 创建分类类型
        $catalogType = new CatalogType();
        $catalogType->setName('产品分类1');
        $catalogType->setCode('product1');
        $catalogType->setDescription('产品分类类型1');
        $catalogTypeRepository = self::getService(CatalogTypeRepository::class);
        self::assertInstanceOf(CatalogTypeRepository::class, $catalogTypeRepository);
        $catalogTypeRepository->save($catalogType);

        // 创建分类
        $catalog = new Catalog();
        $catalog->setType($catalogType);
        $catalog->setName('电子产品1');
        $catalog->setDescription('电子产品分类1');
        $catalog->setSortOrder(1);
        $catalog->setEnabled(true);
        $catalogRepository = self::getService(CatalogRepository::class);
        self::assertInstanceOf(CatalogRepository::class, $catalogRepository);
        $catalogRepository->save($catalog);

        // 创建 SPU
        $spuRepository = self::getService(SpuRepository::class);
        self::assertInstanceOf(SpuRepository::class, $spuRepository);
        $spu = new Spu();
        $spu->setTitle('测试产品1');
        $spu->setGtin('TEST_RELATION_001');
        $spu->setState(SpuState::ONLINE);
        $spuRepository->save($spu);

        // 添加分类到 SPU
        $spu->addCategory($catalog);
        $em->flush();

        // 验证关系
        $categories = $spu->getCategories();
        $this->assertCount(1, $categories);
        $this->assertTrue($categories->contains($catalog));

        // 验证 retrieveAdminArray 方法能正常工作
        $adminArray = $spu->retrieveAdminArray();
        $this->assertIsArray($adminArray);
        $this->assertArrayHasKey('categories', $adminArray);
        $categoriesArray = $adminArray['categories'];
        $this->assertIsArray($categoriesArray);
        $this->assertCount(1, $categoriesArray);
        $this->assertIsArray($categoriesArray[0]);
        $this->assertArrayHasKey('name', $categoriesArray[0]);
        $this->assertEquals('电子产品1', $categoriesArray[0]['name']);

        // 测试移除分类
        $spu->removeCategory($catalog);
        $em->flush();

        $this->assertCount(0, $spu->getCategories());
    }

    public function testSpuWithoutCategoriesRetrieveAdminArray(): void
    {
        self::cleanDatabase();

        // 创建没有分类的 SPU
        $spuRepository = self::getService(SpuRepository::class);
        self::assertInstanceOf(SpuRepository::class, $spuRepository);
        $spu = new Spu();
        $spu->setTitle('无分类产品');
        $spu->setGtin('TEST_NO_CATEGORY_002');
        $spu->setState(SpuState::ONLINE);
        $spuRepository->save($spu);

        // 验证 retrieveAdminArray 方法能正常工作
        $adminArray = $spu->retrieveAdminArray();
        $this->assertIsArray($adminArray);
        $this->assertArrayHasKey('categories', $adminArray);
        $this->assertIsArray($adminArray['categories']);
        $this->assertEmpty($adminArray['categories']);
    }

    public function testMultipleCategoriesForSpu(): void
    {
        self::cleanDatabase();
        $em = self::getEntityManager();

        // 创建分类类型
        $catalogType = new CatalogType();
        $catalogType->setName('产品分类2');
        $catalogType->setCode('product2');
        $catalogType->setDescription('产品分类类型2');
        $catalogTypeRepository = self::getService(CatalogTypeRepository::class);
        self::assertInstanceOf(CatalogTypeRepository::class, $catalogTypeRepository);
        $catalogTypeRepository->save($catalogType);

        // 创建多个分类
        $catalog1 = new Catalog();
        $catalog1->setType($catalogType);
        $catalog1->setName('电子产品2');
        $catalog1->setDescription('电子产品分类2');
        $catalog1->setSortOrder(1);
        $catalog1->setEnabled(true);
        $catalogRepository = self::getService(CatalogRepository::class);
        self::assertInstanceOf(CatalogRepository::class, $catalogRepository);
        $catalogRepository->save($catalog1);

        $catalog2 = new Catalog();
        $catalog2->setType($catalogType);
        $catalog2->setName('数码产品2');
        $catalog2->setDescription('数码产品分类2');
        $catalog2->setSortOrder(2);
        $catalog2->setEnabled(true);
        $catalogRepository->save($catalog2);

        // 创建 SPU
        $spuRepository = self::getService(SpuRepository::class);
        self::assertInstanceOf(SpuRepository::class, $spuRepository);
        $spu = new Spu();
        $spu->setTitle('多分类产品2');
        $spu->setGtin('TEST_MULTI_CATEGORY_002');
        $spu->setState(SpuState::ONLINE);
        $spuRepository->save($spu);

        // 添加多个分类到 SPU
        $spu->addCategory($catalog1);
        $spu->addCategory($catalog2);
        $em->flush();

        // 验证关系
        $categories = $spu->getCategories();
        $this->assertCount(2, $categories);
        $this->assertTrue($categories->contains($catalog1));
        $this->assertTrue($categories->contains($catalog2));

        // 验证 retrieveAdminArray 方法
        $adminArray = $spu->retrieveAdminArray();
        $this->assertIsArray($adminArray);
        $this->assertArrayHasKey('categories', $adminArray);
        $categoriesArray = $adminArray['categories'];
        $this->assertIsArray($categoriesArray);
        $this->assertCount(2, $categoriesArray);

        $categoryNames = array_column($categoriesArray, 'name');
        $this->assertContains('电子产品2', $categoryNames);
        $this->assertContains('数码产品2', $categoryNames);
    }

    /**
     * 测试 SpuRepository::save() 方法
     */
    public function testSavePersistsSpuToDatabase(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);
        $spu = new Spu();
        $spu->setTitle('测试保存产品');
        $spu->setGtin('TEST_SAVE_' . uniqid());

        $spuRepository->save($spu);

        $this->assertNotNull($spu->getId());
        $foundSpu = $spuRepository->find($spu->getId());
        $this->assertInstanceOf(Spu::class, $foundSpu);
        $this->assertSame($spu->getId(), $foundSpu->getId());
    }

    /**
     * 测试 SpuRepository::remove() 方法
     */
    public function testRemoveDeletesSpuFromDatabase(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);
        $spu = new Spu();
        $spu->setTitle('将被删除的产品');
        $spu->setGtin('TEST_REMOVE_' . uniqid());
        $spuRepository->save($spu);

        $savedId = $spu->getId();
        $this->assertNotNull($savedId);

        $spuRepository->remove($spu);

        $removedSpu = $spuRepository->find($savedId);
        $this->assertNull($removedSpu);
    }

    /**
     * 测试 SpuRepository::saveAll() 方法
     */
    public function testSaveAllPersistsBatchOfSpus(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);
        $spuBatch = [];

        for ($i = 1; $i <= 3; ++$i) {
            $spu = new Spu();
            $spu->setTitle("批量保存产品 {$i}");
            $spu->setGtin("BATCH_SPU_{$i}_" . uniqid());
            $spuBatch[] = $spu;
        }

        $spuRepository->saveAll($spuBatch);

        foreach ($spuBatch as $spu) {
            $this->assertNotNull($spu->getId());
        }
    }

    /**
     * 测试 SpuRepository::flush() 方法
     */
    public function testFlushSynchronizesChangesToDatabase(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);
        $spu = new Spu();
        $spu->setTitle('测试刷新产品');
        $spu->setGtin('TEST_FLUSH_' . uniqid());

        // 保存但不立即刷新
        $spuRepository->save($spu, false);
        $spuRepository->flush();

        $this->assertNotNull($spu->getId());
        $foundSpu = $spuRepository->find($spu->getId());
        $this->assertInstanceOf(Spu::class, $foundSpu);
    }

    /**
     * 测试 SpuRepository::clear() 方法
     */
    public function testClearDetachesAllEntitiesFromManager(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);
        $entityManager = self::getEntityManager();

        $spu = new Spu();
        $spu->setTitle('测试清理产品');
        $spu->setGtin('TEST_CLEAR_' . uniqid());
        $spuRepository->save($spu);

        $this->assertTrue($entityManager->contains($spu));

        $spuRepository->clear();

        $this->assertFalse($entityManager->contains($spu));
    }

    /**
     * 测试 SpuRepository::createSpu() 方法
     */
    public function testCreateSpuCreatesNewSpuInstance(): void
    {
        $spuRepository = self::getService(SpuRepository::class);

        $gtin = 'TEST_CREATE_SPU_' . uniqid();
        $title = '创建的测试产品';
        $remark = '测试备注';

        $createdSpu = $spuRepository->createSpu($gtin, $title, $remark, true);

        $this->assertInstanceOf(Spu::class, $createdSpu);
        $this->assertSame($gtin, $createdSpu->getGtin());
        $this->assertSame($title, $createdSpu->getTitle());
        $this->assertSame($remark, $createdSpu->getRemark());
        $this->assertTrue($createdSpu->isValid());
    }

    /**
     * 测试 SpuRepository::createSpu() 的默认参数
     */
    public function testCreateSpuWithDefaultParametersCreatesValidSpu(): void
    {
        $spuRepository = self::getService(SpuRepository::class);

        $defaultSpu = $spuRepository->createSpu();

        $this->assertInstanceOf(Spu::class, $defaultSpu);
        $this->assertNull($defaultSpu->getGtin());
        $this->assertSame('', $defaultSpu->getTitle());
        $this->assertNull($defaultSpu->getRemark());
        $this->assertTrue($defaultSpu->isValid());
    }

    /**
     * 测试 SpuRepository::loadOrCreateSpu() 方法 - 创建新产品
     */
    public function testLoadOrCreateSpuCreatesNewSpuWhenNotExists(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);
        $gtin = 'TEST_LOAD_OR_CREATE_' . uniqid();
        $title = '加载或创建的产品';

        $spu = $spuRepository->loadOrCreateSpu($gtin, $title, null, true);

        $this->assertInstanceOf(Spu::class, $spu);
        $this->assertNotNull($spu->getId());
        $this->assertSame($gtin, $spu->getGtin());
        $this->assertSame($title, $spu->getTitle());
    }

    /**
     * 测试 SpuRepository::loadOrCreateSpu() 方法 - 加载已存在产品
     */
    public function testLoadOrCreateSpuReturnsExistingSpuWhenExists(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);
        $gtin = 'TEST_EXISTING_' . uniqid();
        $originalTitle = '原始标题';

        // 先创建一个
        $firstSpu = $spuRepository->loadOrCreateSpu($gtin, $originalTitle, null, true);
        $this->assertInstanceOf(Spu::class, $firstSpu);

        // 再次调用应该返回已存在的
        $secondSpu = $spuRepository->loadOrCreateSpu($gtin, '不同的标题', '不同的备注', false);
        $this->assertInstanceOf(Spu::class, $secondSpu);

        $this->assertSame($firstSpu->getId(), $secondSpu->getId());
        $this->assertSame($originalTitle, $secondSpu->getTitle()); // 应该保持原来的标题
    }

    /**
     * 测试 SpuRepository::loadOrCreateSpu() 没有GTIN的情况
     */
    public function testLoadOrCreateSpuWithoutGtinCreatesNewSpuEachTime(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);

        $spu1 = $spuRepository->loadOrCreateSpu(null, '无GTIN产品1', null, true);
        $spu2 = $spuRepository->loadOrCreateSpu(null, '无GTIN产品2', null, true);

        $this->assertInstanceOf(Spu::class, $spu1);
        $this->assertInstanceOf(Spu::class, $spu2);
        $this->assertNotSame($spu1->getId(), $spu2->getId());
    }

    /**
     * 测试 SpuRepository::loadSpuByIdentifier() 方法
     */
    public function testLoadSpuByIdentifierReturnsSpuWhenExists(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);
        $gtin = 'TEST_LOAD_BY_ID_' . uniqid();

        $originalSpu = $spuRepository->loadOrCreateSpu($gtin, '测试产品', null, true);
        $this->assertInstanceOf(Spu::class, $originalSpu);

        $loadedSpu = $spuRepository->loadSpuByIdentifier($gtin);

        $this->assertNotNull($loadedSpu);
        $this->assertInstanceOf(Spu::class, $loadedSpu);
        $this->assertSame($originalSpu->getId(), $loadedSpu->getId());
    }

    /**
     * 测试 SpuRepository::loadSpuByIdentifier() 空标识符的处理
     */
    public function testLoadSpuByIdentifierReturnsNullForEmptyIdentifier(): void
    {
        $spuRepository = self::getService(SpuRepository::class);

        $result = $spuRepository->loadSpuByIdentifier('');

        $this->assertNull($result);
    }

    /**
     * 测试 SpuRepository::loadSpuByIdentifier() 不存在的标识符
     */
    public function testLoadSpuByIdentifierReturnsNullWhenNotExists(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);

        $result = $spuRepository->loadSpuByIdentifier('NON_EXISTENT_IDENTIFIER');

        $this->assertNull($result);
    }

    /**
     * 测试 SpuRepository::findByGtin() 方法
     */
    public function testFindByGtinReturnsSpuWhenExists(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);
        $gtin = 'TEST_FIND_BY_GTIN_' . uniqid();

        $originalSpu = $spuRepository->loadOrCreateSpu($gtin, '测试产品', null, true);
        $this->assertInstanceOf(Spu::class, $originalSpu);

        $foundSpu = $spuRepository->findByGtin($gtin);

        $this->assertNotNull($foundSpu);
        $this->assertInstanceOf(Spu::class, $foundSpu);
        $this->assertSame($originalSpu->getId(), $foundSpu->getId());
    }

    /**
     * 测试 SpuRepository::findByGtin() 的排除功能
     */
    public function testFindByGtinWithExcludeIdReturnsNull(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);
        $gtin = 'TEST_FIND_EXCLUDE_' . uniqid();

        $spu = $spuRepository->loadOrCreateSpu($gtin, '测试产品', null, true);
        $this->assertInstanceOf(Spu::class, $spu);

        $excludedResult = $spuRepository->findByGtin($gtin, $spu->getId());

        $this->assertNull($excludedResult);
    }

    /**
     * 测试 SpuRepository::findByGtin() 空GTIN的处理
     */
    public function testFindByGtinReturnsNullForEmptyGtin(): void
    {
        $spuRepository = self::getService(SpuRepository::class);

        $result = $spuRepository->findByGtin('');

        $this->assertNull($result);
    }

    /**
     * 测试 SpuRepository::findByGtin() 不存在的GTIN
     */
    public function testFindByGtinReturnsNullWhenNotExists(): void
    {
        self::cleanDatabase();

        $spuRepository = self::getService(SpuRepository::class);

        $result = $spuRepository->findByGtin('NON_EXISTENT_GTIN');

        $this->assertNull($result);
    }

    /**
     * 集成测试：验证 SpuRepository 在 SPU-Catalog 关系中的行为
     * 这个方法保留了原有的集成测试逻辑，验证多个方法的协同工作
     */
    public function testSpuRepositoryIntegrationWithCatalogRelations(): void
    {
        self::cleanDatabase();
        $em = self::getEntityManager();

        // 创建分类类型
        $catalogType = new CatalogType();
        $catalogType->setName('集成测试分类');
        $catalogType->setCode('integration_test');
        $catalogType->setDescription('集成测试产品分类类型');
        $catalogTypeRepository = self::getService(CatalogTypeRepository::class);
        self::assertInstanceOf(CatalogTypeRepository::class, $catalogTypeRepository);
        $catalogTypeRepository->save($catalogType);

        // 创建分类
        $catalog = new Catalog();
        $catalog->setType($catalogType);
        $catalog->setName('集成测试产品分类');
        $catalog->setDescription('集成测试产品分类');
        $catalog->setSortOrder(1);
        $catalog->setEnabled(true);
        $catalogRepository = self::getService(CatalogRepository::class);
        self::assertInstanceOf(CatalogRepository::class, $catalogRepository);
        $catalogRepository->save($catalog);

        $spuRepository = self::getService(SpuRepository::class);
        self::assertInstanceOf(SpuRepository::class, $spuRepository);

        // 使用 loadOrCreateSpu 创建 SPU
        $gtin = 'INTEGRATION_TEST_' . uniqid();
        $spu = $spuRepository->loadOrCreateSpu($gtin, '集成测试产品', '集成测试备注', true);

        // 验证 SPU 被正确创建
        $this->assertInstanceOf(Spu::class, $spu);
        $this->assertNotNull($spu->getId());
        $this->assertSame($gtin, $spu->getGtin());

        // 使用 findByGtin 查找
        $foundSpu = $spuRepository->findByGtin($gtin);
        $this->assertNotNull($foundSpu);
        $this->assertSame($spu->getId(), $foundSpu->getId());

        // 使用 loadSpuByIdentifier 查找
        $loadedSpu = $spuRepository->loadSpuByIdentifier($gtin);
        $this->assertNotNull($loadedSpu);
        $this->assertInstanceOf(Spu::class, $loadedSpu);
        $this->assertSame($spu->getId(), $loadedSpu->getId());

        // 添加分类到 SPU
        $spu->addCategory($catalog);
        $spuRepository->flush();

        // 验证关系
        $categories = $spu->getCategories();
        $this->assertCount(1, $categories);
        $this->assertTrue($categories->contains($catalog));

        // 验证 clear 方法
        $this->assertTrue($em->contains($spu));
        $spuRepository->clear();

        // 重新加载实体来继续测试
        $reloadedSpu = $spuRepository->find($spu->getId());
        $this->assertNotNull($reloadedSpu);

        // 测试 remove 方法
        $spuRepository->remove($reloadedSpu);

        $removedSpu = $spuRepository->find($spu->getId());
        $this->assertNull($removedSpu);
    }

    /**
     * 集成测试：验证 saveAll 方法在复杂场景中的表现
     */
    public function testSaveAllIntegrationWithCatalogRelations(): void
    {
        self::cleanDatabase();
        $em = self::getEntityManager();

        // 创建分类类型
        $catalogType = new CatalogType();
        $catalogType->setName('批量测试分类');
        $catalogType->setCode('batch_test');
        $catalogType->setDescription('批量测试产品分类类型');
        $catalogTypeRepository = self::getService(CatalogTypeRepository::class);
        self::assertInstanceOf(CatalogTypeRepository::class, $catalogTypeRepository);
        $catalogTypeRepository->save($catalogType);

        // 创建多个分类
        $catalogs = [];
        for ($i = 1; $i <= 2; ++$i) {
            $catalog = new Catalog();
            $catalog->setType($catalogType);
            $catalog->setName("批量测试分类 {$i}");
            $catalog->setDescription("批量测试产品分类 {$i}");
            $catalog->setSortOrder($i);
            $catalog->setEnabled(true);
            $catalogs[] = $catalog;
        }
        $catalogRepository = self::getService(CatalogRepository::class);
        foreach ($catalogs as $catalog) {
            $catalogRepository->save($catalog, false);
        }
        $em->flush();

        $spuRepository = self::getService(SpuRepository::class);

        // 使用 createSpu 创建多个 SPU
        $spus = [];
        for ($i = 1; $i <= 3; ++$i) {
            $spu = $spuRepository->createSpu(
                "BATCH_TEST_{$i}_" . uniqid(),
                "批量测试产品 {$i}",
                "批量测试备注 {$i}",
                true
            );
            $this->assertInstanceOf(Spu::class, $spu);
            // 为每个 SPU 添加分类
            foreach ($catalogs as $catalog) {
                $spu->addCategory($catalog);
            }
            $spus[] = $spu;
        }

        // 使用 saveAll 批量保存
        $spuRepository->saveAll($spus);

        // 验证所有 SPU 都被保存
        foreach ($spus as $spu) {
            $this->assertNotNull($spu->getId());
            $this->assertCount(2, $spu->getCategories());
        }

        // 验证可以通过 findByGtin 找到它们
        foreach ($spus as $spu) {
            $gtin = $spu->getGtin();
            $this->assertNotNull($gtin);
            $foundSpu = $spuRepository->findByGtin($gtin);
            $this->assertNotNull($foundSpu);
            $this->assertSame($spu->getId(), $foundSpu->getId());
        }
    }
}
