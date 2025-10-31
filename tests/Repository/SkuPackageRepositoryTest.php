<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\SkuPackage;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Enum\PackageType;
use Tourze\ProductCoreBundle\Repository\SkuPackageRepository;

/**
 * @internal
 */
#[CoversClass(SkuPackageRepository::class)]
#[RunTestsInSeparateProcesses]
final class SkuPackageRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): SkuPackage
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for SkuPackage ' . uniqid());

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个'); // 设置必需的单位字段

        $entity = new SkuPackage();
        $entity->setSku($sku);
        $entity->setQuantity(5);
        $entity->setType(PackageType::COUPON);
        $entity->setValue('SKU123456');

        // 显式持久化关联实体
        self::getEntityManager()->persist($spu);
        self::getEntityManager()->persist($sku);

        return $entity;
    }

    protected function getRepository(): SkuPackageRepository
    {
        return self::getService(SkuPackageRepository::class);
    }

    protected function onSetUp(): void
    {
        // 父类要求实现此方法，但我们可以留空
    }

    public function testSave(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for SkuPackage');

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个');

        $entity = new SkuPackage();
        $entity->setSku($sku);
        $entity->setQuantity(5);
        $entity->setType(PackageType::COUPON);
        $entity->setValue('SKU123456');

        $entityManager = self::getEntityManager();
        $entityManager->persist($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveAll(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for SkuPackage');

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个');

        $entity = new SkuPackage();
        $entity->setSku($sku);
        $entity->setQuantity(5);
        $entity->setType(PackageType::COUPON);
        $entity->setValue('SKU123456');

        $entityManager = self::getEntityManager();
        $entityManager->persist($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testRemove(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for SkuPackage');

        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个');

        $entity = new SkuPackage();
        $entity->setSku($sku);
        $entity->setQuantity(5);
        $entity->setType(PackageType::COUPON);
        $entity->setValue('SKU123456');

        $entityManager = self::getEntityManager();
        $entityManager->persist($entity);
        $this->assertNotNull($entity->getId());
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
