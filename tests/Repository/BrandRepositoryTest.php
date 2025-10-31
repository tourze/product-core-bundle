<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\ProductCoreBundle\Entity\Brand;
use Tourze\ProductCoreBundle\Repository\BrandRepository;

/**
 * @internal
 */
#[CoversClass(BrandRepository::class)]
#[RunTestsInSeparateProcesses]
final class BrandRepositoryTest extends AbstractRepositoryTestCase
{
    private BrandRepository $repository;

    protected function onSetUp(): void
    {
        // 使用服务注入方式获取Repository
        $this->repository = self::getService(BrandRepository::class);
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
        $repository = self::getService(BrandRepository::class);
        $this->assertInstanceOf(BrandRepository::class, $repository);
    }

    public function testSave(): void
    {
        $entity = new Brand();
        $entity->setName('Test Brand');
        $entity->setValid(true);

        $this->repository->save($entity, false);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveAll(): void
    {
        $entities = [];
        for ($i = 1; $i <= 3; ++$i) {
            $entity = new Brand();
            $entity->setName("Test Brand {$i}");
            $entity->setValid(true);
            $entities[] = $entity;
        }

        $this->repository->saveAll($entities, false);

        foreach ($entities as $entity) {
            $this->assertNotNull($entity->getId());
        }
    }

    public function testRemove(): void
    {
        $entity = new Brand();
        $entity->setName('Test Brand for Remove');
        $entity->setValid(true);

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
        $this->repository->save($entity, false);

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

    public function testFindByWithNullValidField(): void
    {
        $entity = new Brand();
        $entity->setName('Null Valid Test Brand');
        $entity->setValid(null);

        $this->repository->save($entity);

        $nullResults = $this->repository->findBy(['valid' => null]);
        $this->assertIsArray($nullResults);

        $foundTestBrand = false;
        foreach ($nullResults as $result) {
            if ('Null Valid Test Brand' === $result->getName()) {
                $foundTestBrand = true;
                break;
            }
        }
        $this->assertTrue($foundTestBrand, 'Should find brand with null valid field');
    }

    public function testFindByWithNullLogoUrl(): void
    {
        $entity = new Brand();
        $entity->setName('Null Logo Test Brand');
        $entity->setValid(true);
        $entity->setLogoUrl(null);

        $this->repository->save($entity);

        $nullResults = $this->repository->findBy(['logoUrl' => null]);
        $this->assertIsArray($nullResults);

        $foundTestBrand = false;
        foreach ($nullResults as $result) {
            if ('Null Logo Test Brand' === $result->getName()) {
                $foundTestBrand = true;
                break;
            }
        }
        $this->assertTrue($foundTestBrand, 'Should find brand with null logoUrl');
    }

    public function testCountWithNullValidField(): void
    {
        $initialNullCount = $this->repository->count(['valid' => null]);

        $entity = new Brand();
        $entity->setName('Count Null Valid Test Brand');
        $entity->setValid(null);

        $this->repository->save($entity);

        $nullCount = $this->repository->count(['valid' => null]);
        $this->assertEquals($initialNullCount + 1, $nullCount);
    }

    public function testCountWithNullLogoUrl(): void
    {
        $initialNullCount = $this->repository->count(['logoUrl' => null]);

        $entity = new Brand();
        $entity->setName('Count Null Logo Test Brand');
        $entity->setValid(true);
        $entity->setLogoUrl(null);

        $this->repository->save($entity);

        $nullCount = $this->repository->count(['logoUrl' => null]);
        $this->assertEquals($initialNullCount + 1, $nullCount);
    }

    public function testFindOneByOrderByLogic(): void
    {
        $brand1 = new Brand();
        $brand1->setName('B Brand Order Test');
        $brand1->setValid(true);

        $brand2 = new Brand();
        $brand2->setName('A Brand Order Test');
        $brand2->setValid(true);

        $this->repository->save($brand1, false);
        $this->repository->save($brand2, false);
        $this->repository->flush();

        $results = $this->repository->findBy(['valid' => true], ['name' => 'ASC']);
        $testBrands = [];
        foreach ($results as $brand) {
            $brandName = $brand->getName();
            if (null !== $brandName && str_contains($brandName, 'Brand Order Test')) {
                $testBrands[] = $brand;
            }
        }

        $this->assertCount(2, $testBrands);
        $this->assertEquals('A Brand Order Test', $testBrands[0]->getName());
        $this->assertEquals('B Brand Order Test', $testBrands[1]->getName());
    }

    private function cleanupTestData(): void
    {
        // 检查数据库连接是否可用
        if (!self::getEntityManager()->isOpen() || !self::getEntityManager()->getConnection()->isConnected()) {
            return;
        }

        try {
            $testBrands = self::getEntityManager()->createQuery(
                'SELECT b FROM Tourze\ProductCoreBundle\Entity\Brand b WHERE ' .
                'b.name LIKE :pattern1 OR b.name LIKE :pattern2 OR b.name LIKE :pattern3 OR ' .
                'b.name LIKE :pattern4 OR b.name LIKE :pattern5 OR b.name LIKE :pattern6 OR ' .
                'b.name LIKE :pattern7 OR b.name LIKE :pattern8 OR b.name LIKE :pattern9'
            )
                ->setParameter('pattern1', '%Test Brand%')
                ->setParameter('pattern2', '%FindBy Test%')
                ->setParameter('pattern3', '%Limit Test%')
                ->setParameter('pattern4', '%Count%Test%')
                ->setParameter('pattern5', '%Brand')
                ->setParameter('pattern6', '%FindOneBy Test%')
                ->setParameter('pattern7', '%Find Test%')
                ->setParameter('pattern8', '%Null%Test%')
                ->setParameter('pattern9', '%Remove%')
                ->getResult()
            ;

            // 添加类型检查以确保安全的foreach迭代
            if (is_iterable($testBrands)) {
                foreach ($testBrands as $brand) {
                    if ($brand instanceof Brand) {
                        self::getEntityManager()->remove($brand);
                    }
                }
            }

            self::getEntityManager()->flush();
        } catch (\Exception $e) {
            // 忽略数据库清理失败的情况（例如连接不可用）
        }
    }

    protected function createNewEntity(): Brand
    {
        $entity = new Brand();
        $entity->setName('Test Brand ' . uniqid());
        $entity->setValid(true);

        return $entity;
    }

    /**
     * @return BrandRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
