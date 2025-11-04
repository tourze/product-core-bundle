<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\Entity\Sku;
use Tourze\ProductCoreBundle\Entity\SkuAttribute;
use Tourze\ProductCoreBundle\Entity\Spu;
use Tourze\ProductCoreBundle\Entity\SpuAttribute;
use Tourze\ProductCoreBundle\Service\ProductAttributeManager;

/**
 * @internal
 */
#[CoversClass(ProductAttributeManager::class)]
#[RunTestsInSeparateProcesses]
class ProductAttributeManagerTest extends AbstractIntegrationTestCase
{
    private ?ProductAttributeManager $service = null;

    
  
    private function getProductAttributeManager(): ProductAttributeManager
    {
        if (!isset($this->service)) {
            $this->service = self::getService(ProductAttributeManager::class);
        }
        return $this->service;
    }

    protected function onSetUp(): void
    {
        // AbstractIntegrationTestCase required method
    }

  
    
    public function testServiceIsAvailable(): void
    {
        $this->assertInstanceOf(ProductAttributeManager::class, $this->getProductAttributeManager());
    }

    
    public function testEntityManagerIsAvailable(): void
    {
        $entityManager = self::getEntityManager();
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);
    }

    
    public function testBatchSetSpuAttributes(): void
    {
        // 创建测试SPU
        $entityManager = self::getEntityManager();
        $spu = new Spu();
        $spu->setTitle('Test SPU');
        $entityManager->persist($spu);
        $entityManager->flush();

        // 测试批量设置SPU属性
        $attributesData = [
            ['name' => '品牌', 'value' => 'Nike'],
            ['name' => '产地', 'value' => '中国'],
            ['name' => '颜色', 'value' => '红色'],
        ];

        $result = $this->getProductAttributeManager()->batchSetSpuAttributes($spu, $attributesData);

        $this->assertCount(3, $result);
        foreach ($result as $spuAttribute) {
            $this->assertInstanceOf(SpuAttribute::class, $spuAttribute);
            $this->assertSame($spu, $spuAttribute->getSpu());
        }

        // 验证属性已正确保存
        $savedAttributes = $this->getProductAttributeManager()->getSpuAttributes($spu);
        $this->assertCount(3, $savedAttributes);

        $attributeNames = array_map(fn ($attr) => $attr->getName(), $savedAttributes);
        $this->assertContains('品牌', $attributeNames);
        $this->assertContains('产地', $attributeNames);
        $this->assertContains('颜色', $attributeNames);
    }

    public function testBatchSetSkuAttributes(): void
    {
        // 创建测试SKU
        $entityManager = self::getEntityManager();
        $spu = new Spu();
        $spu->setTitle('Test SPU');
        $entityManager->persist($spu);

        $sku = new Sku();
        $sku->setUnit('个');
        $sku->setSpu($spu);
        $entityManager->persist($sku);
        $entityManager->flush();

        // 测试批量设置SKU属性
        $attributesData = [
            ['name' => '尺寸', 'value' => 'L'],
            ['name' => '重量', 'value' => '500g'],
        ];

        $result = $this->getProductAttributeManager()->batchSetSkuAttributes($sku, $attributesData);

        $this->assertCount(2, $result);
        foreach ($result as $skuAttribute) {
            $this->assertInstanceOf(SkuAttribute::class, $skuAttribute);
            $this->assertSame($sku, $skuAttribute->getSku());
        }

        // 验证属性已正确保存
        $savedAttributes = $this->getProductAttributeManager()->getSkuAttributes($sku);
        $this->assertCount(2, $savedAttributes);

        $attributeNames = array_map(fn ($attr) => $attr->getName(), $savedAttributes);
        $this->assertContains('尺寸', $attributeNames);
        $this->assertContains('重量', $attributeNames);
    }

    public function testRemoveSpuAttribute(): void
    {
        // 创建测试数据
        $entityManager = self::getEntityManager();
        $spu = new Spu();
        $spu->setTitle('Test SPU');
        $entityManager->persist($spu);

        $spuAttribute = new SpuAttribute();
        $spuAttribute->setSpu($spu);
        $spuAttribute->setName('测试属性');
        $spuAttribute->setValue('测试值');
        $entityManager->persist($spuAttribute);
        $entityManager->flush();

        $attributeId = $spuAttribute->getId();

        // 删除属性
        $this->getProductAttributeManager()->removeSpuAttribute($spuAttribute);

        // 验证属性已被删除
        $deletedAttribute = $entityManager->find(SpuAttribute::class, $attributeId);
        $this->assertNull($deletedAttribute);
    }

    public function testAssignSkuAttributeValue(): void
    {
        // 创建测试SKU
        $entityManager = self::getEntityManager();
        $spu = new Spu();
        $spu->setTitle('Test SPU');
        $entityManager->persist($spu);

        $sku = new Sku();
        $sku->setUnit('个');
        $sku->setSpu($spu);
        $entityManager->persist($sku);
        $entityManager->flush();

        // 测试新建属性
        $result = $this->getProductAttributeManager()->assignSkuAttributeValue($sku, '颜色', '红色');
        $entityManager->flush(); // 确保第一次操作完整持久化

        $this->assertInstanceOf(SkuAttribute::class, $result);
        $this->assertSame($sku, $result->getSku());
        $this->assertEquals('颜色', $result->getName());
        $this->assertEquals('红色', $result->getValue());
        $this->assertIsString($result->getId()); // 确保ID已生成

        // 保存 SKU ID 用于后续查找
        $skuId = $sku->getId();

        // 清除实体管理器，强制从数据库重新加载
        $entityManager->clear();

        // 重新获取 SKU 实体
        $sku = $entityManager->find(Sku::class, $skuId);
        $this->assertInstanceOf(Sku::class, $sku);

        // 测试更新已存在的属性
        $updatedResult = $this->getProductAttributeManager()->assignSkuAttributeValue($sku, '颜色', '蓝色');
        $entityManager->flush();

        $this->assertInstanceOf(SkuAttribute::class, $updatedResult);
        $this->assertEquals('颜色', $updatedResult->getName());
        $this->assertEquals('蓝色', $updatedResult->getValue()); // 值已更新
        $this->assertIsString($updatedResult->getId());

        // 验证数据库中只有一条记录
        $attributes = $this->getProductAttributeManager()->getSkuAttributes($sku);
        $colorAttributes = array_filter($attributes, fn ($attr) => '颜色' === $attr->getName());
        $this->assertCount(1, $colorAttributes);

        $colorAttribute = reset($colorAttributes);
        $this->assertEquals('蓝色', $colorAttribute->getValue());
    }

    public function testAssignSpuAttributeValue(): void
    {
        // 创建测试SPU
        $entityManager = self::getEntityManager();
        $spu = new Spu();
        $spu->setTitle('Test SPU');
        $entityManager->persist($spu);
        $entityManager->flush();

        // 测试新建属性
        $result = $this->getProductAttributeManager()->assignSpuAttributeValue($spu, '品牌', 'Nike');
        $entityManager->flush(); // 确保第一次操作完整持久化

        $this->assertInstanceOf(SpuAttribute::class, $result);
        $this->assertSame($spu, $result->getSpu());
        $this->assertEquals('品牌', $result->getName());
        $this->assertEquals('Nike', $result->getValue());
        $this->assertIsString($result->getId()); // 确保ID已生成

        // 保存 SPU ID 用于后续查找
        $spuId = $spu->getId();

        // 清除实体管理器，强制从数据库重新加载
        $entityManager->clear();

        // 重新获取 SPU 实体
        $spu = $entityManager->find(Spu::class, $spuId);
        $this->assertInstanceOf(Spu::class, $spu);

        // 测试更新已存在的属性
        $updatedResult = $this->getProductAttributeManager()->assignSpuAttributeValue($spu, '品牌', 'Adidas');
        $entityManager->flush();

        $this->assertInstanceOf(SpuAttribute::class, $updatedResult);
        $this->assertEquals('品牌', $updatedResult->getName());
        $this->assertEquals('Adidas', $updatedResult->getValue()); // 值已更新
        $this->assertIsString($updatedResult->getId());

        // 验证数据库中只有一条记录
        $attributes = $this->getProductAttributeManager()->getSpuAttributes($spu);
        $brandAttributes = array_filter($attributes, fn ($attr) => '品牌' === $attr->getName());
        $this->assertCount(1, $brandAttributes);

        $brandAttribute = reset($brandAttributes);
        $this->assertEquals('Adidas', $brandAttribute->getValue());
    }

    public function testRemoveSkuAttribute(): void
    {
        // 创建测试数据
        $entityManager = self::getEntityManager();
        $spu = new Spu();
        $spu->setTitle('Test SPU');
        $entityManager->persist($spu);

        $sku = new Sku();
        $sku->setUnit('个');
        $sku->setSpu($spu);
        $entityManager->persist($sku);

        $skuAttribute = new SkuAttribute();
        $skuAttribute->setSku($sku);
        $skuAttribute->setName('测试SKU属性');
        $skuAttribute->setValue('测试SKU值');
        $entityManager->persist($skuAttribute);
        $entityManager->flush();

        $attributeId = $skuAttribute->getId();

        // 删除属性
        $this->getProductAttributeManager()->removeSkuAttribute($skuAttribute);

        // 验证属性已被删除
        $deletedAttribute = $entityManager->find(SkuAttribute::class, $attributeId);
        $this->assertNull($deletedAttribute);
    }
}
