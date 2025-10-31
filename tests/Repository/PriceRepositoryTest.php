<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\ProductCoreBundle\Entity\Price;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Enum\PriceType;
use Tourze\ProductCoreBundle\Repository\PriceRepository;

/**
 * @internal
 */
#[CoversClass(PriceRepository::class)]
#[RunTestsInSeparateProcesses]
final class PriceRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): Price
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for Price ' . uniqid());

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个'); // 设置必需的单位字段

        $entity = new Price();
        $entity->setSku($sku);
        $entity->setType(PriceType::SALE);
        $entity->setCurrency('CNY');
        $entity->setPrice('100.00');

        // 显式持久化关联实体
        self::getEntityManager()->persist($spu);
        self::getEntityManager()->persist($sku);

        return $entity;
    }

    protected function getRepository(): PriceRepository
    {
        return self::getService(PriceRepository::class);
    }

    protected function onSetUp(): void
    {
        // 父类要求实现此方法，但我们可以留空
    }

    public function testSave(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for Price');

        $sku = new Sku();
        $sku->setSpu($spu);

        $entity = new Price();
        $entity->setSku($sku);
        $entity->setType(PriceType::SALE);
        $entity->setCurrency('CNY');
        $entity->setPrice('100.00');

        $this->getRepository()->save($entity, false);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveAll(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for Price Batch');

        $sku = new Sku();
        $sku->setSpu($spu);

        $entities = [];
        for ($i = 1; $i <= 3; ++$i) {
            $entity = new Price();
            $entity->setSku($sku);
            $entity->setType(PriceType::SALE);
            $entity->setCurrency('CNY');
            $entity->setPrice((string) (100 + $i));
            $entities[] = $entity;
        }

        $this->getRepository()->saveAll($entities, false);

        foreach ($entities as $entity) {
            $this->assertNotNull($entity->getId());
        }
    }

    public function testRemove(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for Remove Price');

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个'); // 设置必需的单位字段

        $entity = new Price();
        $entity->setSku($sku);
        $entity->setType(PriceType::SALE);
        $entity->setCurrency('CNY');
        $entity->setPrice('100.00');

        // 先持久化关联的实体
        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->persist($sku);
        $entityManager->flush();

        $this->getRepository()->save($entity);
        $savedId = $entity->getId();

        $this->getRepository()->remove($entity, false);
        $this->getRepository()->flush();

        $found = $this->getRepository()->find($savedId);
        $this->assertNull($found);
    }

    public function testFlush(): void
    {
        $entity = $this->createNewEntity();
        $entityManager = self::getEntityManager();
        $entityManager->persist($entity);

        $this->getRepository()->flush();
        $this->assertNotNull($entity->getId());
    }

    public function testClear(): void
    {
        $entity = $this->createNewEntity();
        $this->getRepository()->save($entity);

        $entityManager = self::getEntityManager();
        $this->assertTrue($entityManager->contains($entity));

        $this->getRepository()->clear();
        $this->assertFalse($entityManager->contains($entity));
    }
}
