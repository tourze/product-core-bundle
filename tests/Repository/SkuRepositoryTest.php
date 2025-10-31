<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Repository\SkuRepository;

/**
 * @internal
 */
#[CoversClass(SkuRepository::class)]
#[RunTestsInSeparateProcesses]
final class SkuRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): Sku
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for Sku ' . uniqid());

        $entity = new Sku();
        $entity->setSpu($spu);
        $entity->setUnit('个'); // 设置必需的单位字段

        // 显式持久化关联实体
        self::getEntityManager()->persist($spu);

        return $entity;
    }

    protected function getRepository(): SkuRepository
    {
        return self::getService(SkuRepository::class);
    }

    protected function onSetUp(): void
    {
        // 父类要求实现此方法，但我们可以留空
    }

    public function testSave(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for Sku');

        $entity = new Sku();
        $entity->setSpu($spu);
        $entity->setUnit('个'); // 设置必需的单位字段

        $entityManager = self::getEntityManager();
        $entityManager->persist($entity);
        $this->assertNotNull($entity->getId());
    }

    public function testSaveAll(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for Sku Batch');

        $entities = [];
        for ($i = 1; $i <= 3; ++$i) {
            $entity = new Sku();
            $entity->setSpu($spu);
            $entity->setUnit('个'); // 设置必需的单位字段
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
        $spu->setTitle('Test SPU for Remove Sku');

        $entity = new Sku();
        $entity->setSpu($spu);
        $entity->setUnit('个'); // 设置必需的单位字段

        // 先持久化关联的实体
        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
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

    public function testCreateSku(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for Create Sku');

        // 先持久化SPU
        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->flush();

        $gtin = 'test-gtin-' . uniqid();
        $mpn = 'test-mpn-' . uniqid();
        $remark = 'Test remark';

        $sku = $this->getRepository()->createSku(
            $spu,
            $gtin,
            $mpn,
            $remark,
            true
        );

        $this->assertInstanceOf(Sku::class, $sku);
        $this->assertNotNull($sku->getId());
        $this->assertSame($spu, $sku->getSpu());
        $this->assertSame($gtin, $sku->getGtin());
        $this->assertSame($mpn, $sku->getMpn());
        $this->assertSame($remark, $sku->getRemark());
        $this->assertTrue($sku->isValid());
        $this->assertSame('个', $sku->getUnit());
    }

    public function testFindAllValid(): void
    {
        // 先清理现有数据确保测试环境干净
        $this->clearExistingTestData();

        // 创建测试数据
        $spu1 = new Spu();
        $spu1->setTitle('AAA Test SPU Valid');
        $spu2 = new Spu();
        $spu2->setTitle('BBB Test SPU Valid');

        $entityManager = self::getEntityManager();
        $entityManager->persist($spu1);
        $entityManager->persist($spu2);
        $entityManager->flush();

        // 创建有效的SKU
        $validSku1 = new Sku();
        $validSku1->setSpu($spu1);
        $validSku1->setUnit('个');
        $validSku1->setValid(true);
        $validSku1->setGtin('VALID-SKU-1');

        $validSku2 = new Sku();
        $validSku2->setSpu($spu2);
        $validSku2->setUnit('个');
        $validSku2->setValid(true);
        $validSku2->setGtin('VALID-SKU-2');

        // 创建无效的SKU
        $invalidSku = new Sku();
        $invalidSku->setSpu($spu1);
        $invalidSku->setUnit('个');
        $invalidSku->setValid(false);
        $invalidSku->setGtin('INVALID-SKU-1');

        $this->getRepository()->save($validSku1);
        $this->getRepository()->save($validSku2);
        $this->getRepository()->save($invalidSku);

        $validSkus = $this->getRepository()->findAllValid();

        // 过滤出我们的测试数据
        $testValidSkus = [];
        foreach ($validSkus as $sku) {
            if (in_array($sku->getGtin(), ['VALID-SKU-1', 'VALID-SKU-2'], true)) {
                $testValidSkus[] = $sku;
            }
        }

        // 验证只返回有效的SKU，且按SPU标题排序
        $this->assertCount(2, $testValidSkus);
        $this->assertTrue($testValidSkus[0]->isValid());
        $this->assertTrue($testValidSkus[1]->isValid());

        // 验证排序顺序（按SPU标题排序）
        $spu0 = $testValidSkus[0]->getSpu();
        $spu1 = $testValidSkus[1]->getSpu();
        $this->assertNotNull($spu0);
        $this->assertNotNull($spu1);
        $this->assertSame('AAA Test SPU Valid', $spu0->getTitle());
        $this->assertSame('BBB Test SPU Valid', $spu1->getTitle());
    }

    private function clearExistingTestData(): void
    {
        try {
            $entityManager = self::getEntityManager();

            $this->clearTestEntities($entityManager, 'Tourze\ProductCoreBundle\Entity\Sku', [
                's.gtin LIKE :pattern1 OR s.gtin LIKE :pattern2',
                ['pattern1' => 'VALID-SKU%', 'pattern2' => 'INVALID-SKU%'],
            ]);

            $this->clearTestEntities($entityManager, 'Tourze\ProductCoreBundle\Entity\Spu', [
                's.title LIKE :pattern',
                ['pattern' => '%Test SPU Valid%'],
            ]);

            $entityManager->flush();
        } catch (\Exception $e) {
            // 忽略清理失败
        }
    }

    /**
     * @param array{0: string, 1: array<string,mixed>} $queryAndParams
     */
    private function clearTestEntities(EntityManagerInterface $entityManager, string $entityClass, array $queryAndParams): void
    {
        [$dql, $params] = $queryAndParams;
        $query = $entityManager->createQuery("SELECT s FROM {$entityClass} s WHERE {$dql}");

        foreach ($params as $key => $value) {
            $query->setParameter($key, $value);
        }

        $entities = $query->getResult();

        if (is_array($entities)) {
            foreach ($entities as $entity) {
                if (is_object($entity)) {
                    $entityManager->remove($entity);
                }
            }
        }
    }

    public function testLoadSkuByIdentifier(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for Load');

        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->flush();

        // 测试通过GTIN查找
        $gtin = 'test-gtin-' . uniqid();
        $skuWithGtin = new Sku();
        $skuWithGtin->setSpu($spu);
        $skuWithGtin->setUnit('个');
        $skuWithGtin->setGtin($gtin);
        $skuWithGtin->setValid(true);

        $this->getRepository()->save($skuWithGtin);

        $foundByGtin = $this->getRepository()->loadSkuByIdentifier($gtin);
        $this->assertNotNull($foundByGtin);
        $this->assertSame($skuWithGtin->getId(), $foundByGtin->getId());

        // 测试通过ID查找
        $skuId = $skuWithGtin->getId();
        $foundById = $this->getRepository()->loadSkuByIdentifier($skuId);
        $this->assertNotNull($foundById);
        $this->assertSame($skuWithGtin->getId(), $foundById->getId());

        // 测试查找不存在的标识符
        $notFound = $this->getRepository()->loadSkuByIdentifier('non-existent-identifier');
        $this->assertNull($notFound);

        // 测试无效SKU不会被找到
        $invalidSku = new Sku();
        $invalidSku->setSpu($spu);
        $invalidSku->setUnit('个');
        $invalidSku->setGtin('invalid-gtin');
        $invalidSku->setValid(false);

        $this->getRepository()->save($invalidSku);

        $notFoundInvalid = $this->getRepository()->loadSkuByIdentifier('invalid-gtin');
        $this->assertNull($notFoundInvalid);
    }

    public function testFindByGtin(): void
    {
        $spu = new Spu();
        $spu->setTitle('Test SPU for FindByGtin');

        $entityManager = self::getEntityManager();
        $entityManager->persist($spu);
        $entityManager->flush();

        $gtin = 'test-find-gtin-' . uniqid();

        // 创建测试SKU
        $sku = new Sku();
        $sku->setSpu($spu);
        $sku->setUnit('个');
        $sku->setGtin($gtin);
        $sku->setValid(true);

        $this->getRepository()->save($sku);

        // 测试成功查找
        $foundSku = $this->getRepository()->findByGtin($gtin);
        $this->assertNotNull($foundSku);
        $this->assertInstanceOf(Sku::class, $foundSku);
        $this->assertSame($sku->getId(), $foundSku->getId());
        $this->assertSame($gtin, $foundSku->getGtin());

        // 测试空GTIN
        $emptyGtinResult = $this->getRepository()->findByGtin('');
        $this->assertNull($emptyGtinResult);

        // 测试不存在的GTIN
        $notFoundResult = $this->getRepository()->findByGtin('non-existent-gtin');
        $this->assertNull($notFoundResult);

        // 测试排除ID功能
        $skuId = (int) $sku->getId();
        $excludedResult = $this->getRepository()->findByGtin($gtin, $skuId);
        $this->assertNull($excludedResult);

        // 创建另一个具有相同GTIN的SKU用于测试排除功能
        $sku2 = new Sku();
        $sku2->setSpu($spu);
        $sku2->setUnit('个');
        $sku2->setGtin($gtin);
        $sku2->setValid(true);

        $this->getRepository()->save($sku2);

        // 测试排除第一个SKU，应该找到第二个
        $sku2Id = (int) $sku2->getId();

        $excludedResult2 = $this->getRepository()->findByGtin($gtin, $skuId);
        $this->assertNotNull($excludedResult2);
        $this->assertSame($sku2->getId(), $excludedResult2->getId());

        // 测试排除第二个SKU，应该找到第一个
        $excludedResult3 = $this->getRepository()->findByGtin($gtin, $sku2Id);
        $this->assertNotNull($excludedResult3);
        $this->assertSame($sku->getId(), $excludedResult3->getId());
    }
}
