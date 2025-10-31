<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Repository\SpuRepository;

/**
 * @internal
 */
#[CoversClass(SpuRepository::class)]
#[RunTestsInSeparateProcesses]
final class SpuRepositoryTest extends AbstractRepositoryTestCase
{
    private SpuRepository $repository;

    protected function onSetUp(): void
    {
        // 使用服务注入方式获取Repository
        $this->repository = self::getService(SpuRepository::class);
        $this->cleanupTestData();
    }

    protected function onTearDown(): void
    {
        // 检查数据库连接是否可用
        if (self::getEntityManager()->isOpen() && self::getEntityManager()->getConnection()->isConnected()) {
            $this->cleanupTestData();
        }
    }

    public function testCanBeInstantiated(): void
    {
        $repository = self::getService(SpuRepository::class);
        $this->assertInstanceOf(SpuRepository::class, $repository);
    }

    public function testSave(): void
    {
        $entity = new Spu();
        $entity->setTitle('Test SPU');

        $this->repository->save($entity, false);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveAll(): void
    {
        $entities = [];
        for ($i = 1; $i <= 3; ++$i) {
            $entity = new Spu();
            $entity->setTitle("Test SPU {$i}");
            $entities[] = $entity;
        }

        $this->repository->saveAll($entities, false);

        foreach ($entities as $entity) {
            $this->assertNotNull($entity->getId());
        }
    }

    public function testRemove(): void
    {
        $entity = new Spu();
        $entity->setTitle('Test SPU for Remove');

        $this->repository->save($entity);
        $savedId = $entity->getId();

        $this->repository->remove($entity, false);
        $this->repository->flush();

        $found = $this->repository->find($savedId);
        $this->assertNull($found);
    }

    public function testFlush(): void
    {
        $entity = $this->createNewEntity();
        $entityManager = self::getEntityManager();
        $entityManager->persist($entity);

        $this->repository->flush();
        $this->assertNotNull($entity->getId());
    }

    public function testClear(): void
    {
        $entity = $this->createNewEntity();
        $this->repository->save($entity);

        $entityManager = self::getEntityManager();
        $this->assertTrue($entityManager->contains($entity));

        $this->repository->clear();
        $this->assertFalse($entityManager->contains($entity));
    }

    public function testFindWithNonExistentId(): void
    {
        $found = $this->repository->find(-999999);
        $this->assertNull($found);
    }

    public function testFindByWithNullFields(): void
    {
        $entity = new Spu();
        $entity->setTitle('Null Fields Test SPU');
        $entity->setValid(null);
        $entity->setGtin(null);
        $entity->setType(null);
        $entity->setSubtitle(null);
        $entity->setMainPic(null);
        $entity->setContent(null);
        $entity->setRemark(null);

        $this->repository->save($entity);

        // Test multiple null fields
        $nullValidResults = $this->repository->findBy(['valid' => null]);
        $this->assertIsArray($nullValidResults);

        $foundEntity = false;
        foreach ($nullValidResults as $result) {
            if ('Null Fields Test SPU' === $result->getTitle()) {
                $foundEntity = true;
                break;
            }
        }
        $this->assertTrue($foundEntity, 'Should find SPU with null valid field');
    }

    public function testCountWithNullFields(): void
    {
        $initialNullValidCount = $this->repository->count(['valid' => null]);
        $initialNullRemarkCount = $this->repository->count(['remark' => null]);

        $entity = new Spu();
        $entity->setTitle('Count Null Fields Test SPU');
        $entity->setValid(null);
        $entity->setRemark(null);

        $this->repository->save($entity);

        $nullValidCount = $this->repository->count(['valid' => null]);
        $this->assertEquals($initialNullValidCount + 1, $nullValidCount);

        $nullRemarkCount = $this->repository->count(['remark' => null]);
        $this->assertEquals($initialNullRemarkCount + 1, $nullRemarkCount);
    }

    private function cleanupTestData(): void
    {
        // 检查数据库连接是否可用
        if (!self::getEntityManager()->isOpen() || !self::getEntityManager()->getConnection()->isConnected()) {
            return;
        }

        try {
            $testSpus = self::getEntityManager()->createQuery(
                'SELECT s FROM Tourze\ProductCoreBundle\Entity\Spu s WHERE ' .
                's.title LIKE :pattern1 OR s.title LIKE :pattern2 OR s.title LIKE :pattern3 OR ' .
                's.title LIKE :pattern4 OR s.title LIKE :pattern5 OR s.title LIKE :pattern6 OR ' .
                's.title LIKE :pattern7 OR s.title LIKE :pattern8 OR s.title LIKE :pattern9'
            )
                ->setParameter('pattern1', '%Test SPU%')
                ->setParameter('pattern2', '%FindBy Test%')
                ->setParameter('pattern3', '%Limit Test%')
                ->setParameter('pattern4', '%Count%Test%')
                ->setParameter('pattern5', '%SPU')
                ->setParameter('pattern6', '%FindOneBy Test%')
                ->setParameter('pattern7', '%Find Test%')
                ->setParameter('pattern8', '%Null%Test%')
                ->setParameter('pattern9', '%Remove%')
                ->getResult()
            ;

            // 添加类型检查以确保安全的foreach迭代
            if (is_iterable($testSpus)) {
                foreach ($testSpus as $spu) {
                    if ($spu instanceof Spu) {
                        self::getEntityManager()->remove($spu);
                    }
                }
            }

            self::getEntityManager()->flush();
        } catch (\Exception $e) {
            // 忽略数据库清理失败的情况（例如连接不可用）
        }
    }

    protected function createNewEntity(): Spu
    {
        $entity = new Spu();
        $entity->setTitle('Test SPU ' . uniqid());
        $entity->setGtin('SPU' . uniqid());

        return $entity;
    }

    protected function getRepository(): SpuRepository
    {
        return $this->repository;
    }

    public function testCreateSpu(): void
    {
        $gtin = 'test-gtin-' . uniqid();
        $title = 'Test SPU Title';
        $remark = 'Test remark';

        $spu = $this->repository->createSpu($gtin, $title, $remark, true);

        $this->assertInstanceOf(Spu::class, $spu);
        $this->assertSame($gtin, $spu->getGtin());
        $this->assertSame($title, $spu->getTitle());
        $this->assertSame($remark, $spu->getRemark());
        $this->assertTrue($spu->isValid());

        // 测试默认值
        $spuWithDefaults = $this->repository->createSpu();
        $this->assertInstanceOf(Spu::class, $spuWithDefaults);
        $this->assertNull($spuWithDefaults->getGtin());
        $this->assertSame('', $spuWithDefaults->getTitle());
        $this->assertNull($spuWithDefaults->getRemark());
        $this->assertTrue($spuWithDefaults->isValid());
    }

    public function testLoadOrCreateSpu(): void
    {
        $gtin = 'test-gtin-for-load-or-create-' . uniqid();
        $title = 'Test SPU for Load or Create';
        $remark = 'Test remark';

        // 测试创建新的SPU
        $newSpu = $this->repository->loadOrCreateSpu($gtin, $title, $remark, true);
        $this->assertInstanceOf(Spu::class, $newSpu);
        $this->assertNotNull($newSpu->getId());
        $this->assertSame($gtin, $newSpu->getGtin());
        $this->assertSame($title, $newSpu->getTitle());

        // 测试加载已存在的SPU
        $existingSpu = $this->repository->loadOrCreateSpu($gtin, 'Different Title', 'Different remark', false);
        $this->assertInstanceOf(Spu::class, $existingSpu);
        $this->assertSame($newSpu->getId(), $existingSpu->getId());
        // 应该返回已存在的SPU，而不是使用新的参数创建
        $this->assertSame($title, $existingSpu->getTitle());

        // 测试没有GTIN的情况（应该总是创建新的）
        $spuWithoutGtin1 = $this->repository->loadOrCreateSpu(null, 'SPU 1', null, true);
        $spuWithoutGtin2 = $this->repository->loadOrCreateSpu(null, 'SPU 2', null, true);

        $this->assertInstanceOf(Spu::class, $spuWithoutGtin1);
        $this->assertInstanceOf(Spu::class, $spuWithoutGtin2);
        $this->assertNotSame($spuWithoutGtin1->getId(), $spuWithoutGtin2->getId());
        $this->assertSame('SPU 1', $spuWithoutGtin1->getTitle());
        $this->assertSame('SPU 2', $spuWithoutGtin2->getTitle());
    }

    public function testLoadSpuByIdentifier(): void
    {
        $gtin = 'test-identifier-' . uniqid();

        // 创建并保存SPU
        $spu = new Spu();
        $spu->setTitle('Test SPU for Identifier');
        $spu->setGtin($gtin);
        $this->repository->save($spu);

        // 测试通过GTIN查找
        $foundSpu = $this->repository->loadSpuByIdentifier($gtin);
        $this->assertNotNull($foundSpu);
        $this->assertInstanceOf(Spu::class, $foundSpu);
        $this->assertSame($spu->getId(), $foundSpu->getId());
        $this->assertSame($gtin, $foundSpu->getGtin());

        // 测试查找不存在的标识符
        $notFound = $this->repository->loadSpuByIdentifier('non-existent-identifier');
        $this->assertNull($notFound);

        // 测试空标识符
        $nullResult = $this->repository->loadSpuByIdentifier('');
        $this->assertNull($nullResult);
    }

    public function testFindByGtin(): void
    {
        $gtin = 'test-find-gtin-' . uniqid();

        // 创建并保存SPU
        $spu = new Spu();
        $spu->setTitle('Test SPU for FindByGtin');
        $spu->setGtin($gtin);
        $this->repository->save($spu);

        // 测试成功查找
        $foundSpu = $this->repository->findByGtin($gtin);
        $this->assertNotNull($foundSpu);
        $this->assertInstanceOf(Spu::class, $foundSpu);
        $this->assertSame($spu->getId(), $foundSpu->getId());
        $this->assertSame($gtin, $foundSpu->getGtin());

        // 测试空GTIN
        $emptyGtinResult = $this->repository->findByGtin('');
        $this->assertNull($emptyGtinResult);

        // 测试不存在的GTIN
        $notFoundResult = $this->repository->findByGtin('non-existent-gtin');
        $this->assertNull($notFoundResult);

        // 测试排除ID功能
        $excludedResult = $this->repository->findByGtin($gtin, $spu->getId());
        $this->assertNull($excludedResult);

        // 创建另一个具有不同GTIN的SPU用于测试排除功能
        $gtin2 = 'test-find-gtin-2-' . uniqid();
        $spu2 = new Spu();
        $spu2->setTitle('Test SPU 2 for FindByGtin');
        $spu2->setGtin($gtin2);
        $this->repository->save($spu2);

        // 测试第二个GTIN能正确查找
        $foundSpu2 = $this->repository->findByGtin($gtin2);
        $this->assertNotNull($foundSpu2);
        $this->assertSame($spu2->getId(), $foundSpu2->getId());

        // 测试排除功能：查找第二个GTIN但排除其自身ID
        $excludedResult2 = $this->repository->findByGtin($gtin2, $spu2->getId());
        $this->assertNull($excludedResult2);

        // 测试排除功能：查找第一个GTIN但排除不相关的ID
        $excludedResult3 = $this->repository->findByGtin($gtin, $spu2->getId());
        $this->assertNotNull($excludedResult3);
        $this->assertSame($spu->getId(), $excludedResult3->getId());
    }
}
