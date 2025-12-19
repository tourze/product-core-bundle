<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\Entity\Attribute;
use Tourze\ProductCoreBundle\Entity\AttributeValue;
use Tourze\ProductCoreBundle\Enum\AttributeStatus;
use Tourze\ProductCoreBundle\Enum\AttributeType;
use Tourze\ProductCoreBundle\Exception\AttributeException;
use Tourze\ProductCoreBundle\Repository\AttributeRepository;
use Tourze\ProductCoreBundle\Repository\AttributeValueRepository;
use Tourze\ProductCoreBundle\Service\AttributeManager;

/**
 * @internal
 */
#[CoversClass(AttributeManager::class)]
#[RunTestsInSeparateProcesses]
final class AttributeManagerTest extends AbstractIntegrationTestCase
{
    private AttributeManager $service;

    private AttributeRepository $attributeRepository;

    private AttributeValueRepository $attributeValueRepository;

    protected function onSetUp(): void
    {
        $this->service = self::getService(AttributeManager::class);
        $this->attributeRepository = self::getService(AttributeRepository::class);
        $this->attributeValueRepository = self::getService(AttributeValueRepository::class);
        $this->cleanupTestData();
    }

    protected function onTearDown(): void
    {
        $this->cleanupTestData();
    }

    public function testCreateAttributeSuccess(): void
    {
        $data = [
            'code' => 'test_attr_color',
            'name' => '颜色',
            'type' => AttributeType::SALES,
        ];

        $attribute = $this->service->createAttribute($data);

        $this->assertNotNull($attribute->getId());
        $this->assertEquals('test_attr_color', $attribute->getCode());
        $this->assertEquals('颜色', $attribute->getName());
        $this->assertEquals(AttributeType::SALES, $attribute->getType());

        // 验证数据库持久化
        $em = self::getEntityManager();
        $em->clear();

        $foundAttribute = $this->attributeRepository->find($attribute->getId());
        $this->assertNotNull($foundAttribute);
        $this->assertEquals('test_attr_color', $foundAttribute->getCode());
    }

    public function testCreateAttributeWithDuplicateCode(): void
    {
        // 先创建一个属性
        $data = [
            'code' => 'test_attr_duplicate',
            'name' => '重复属性',
        ];
        $this->service->createAttribute($data);

        // 尝试创建同样 code 的属性
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage('属性编码 "test_attr_duplicate" 已存在');

        $this->service->createAttribute($data);
    }

    public function testUpdateAttributeSuccess(): void
    {
        // 创建初始属性
        $data = [
            'code' => 'test_attr_old_code',
            'name' => 'Old Name',
        ];
        $attribute = $this->service->createAttribute($data);

        // 更新属性
        $updateData = [
            'code' => 'test_attr_new_code',
            'name' => 'New Name',
        ];
        $updatedAttribute = $this->service->updateAttribute($attribute, $updateData);

        $this->assertEquals('test_attr_new_code', $updatedAttribute->getCode());
        $this->assertEquals('New Name', $updatedAttribute->getName());

        // 验证数据库中的更新
        $em = self::getEntityManager();
        $em->clear();

        $foundAttribute = $this->attributeRepository->find($attribute->getId());
        $this->assertNotNull($foundAttribute);
        $this->assertEquals('test_attr_new_code', $foundAttribute->getCode());
        $this->assertEquals('New Name', $foundAttribute->getName());
    }

    public function testUpdateAttributeWithDuplicateCode(): void
    {
        // 创建两个属性
        $attribute1 = $this->service->createAttribute([
            'code' => 'test_attr_existing',
            'name' => 'Existing',
        ]);

        $attribute2 = $this->service->createAttribute([
            'code' => 'test_attr_to_update',
            'name' => 'To Update',
        ]);

        // 尝试将 attribute2 更新为 attribute1 的 code
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage('属性编码 "test_attr_existing" 已存在');

        $this->service->updateAttribute($attribute2, [
            'code' => 'test_attr_existing',
        ]);
    }

    public function testDeleteAttribute(): void
    {
        // 创建属性
        $attribute = $this->service->createAttribute([
            'code' => 'test_attr_to_delete',
            'name' => '待删除属性',
        ]);

        $this->assertEquals(AttributeStatus::ACTIVE, $attribute->getStatus());

        // 删除属性（软删除）
        $this->service->deleteAttribute($attribute);

        $this->assertEquals(AttributeStatus::INACTIVE, $attribute->getStatus());

        // 验证数据库中的状态
        $em = self::getEntityManager();
        $em->clear();

        $foundAttribute = $this->attributeRepository->find($attribute->getId());
        $this->assertNotNull($foundAttribute);
        $this->assertEquals(AttributeStatus::INACTIVE, $foundAttribute->getStatus());
    }

    public function testActivateAttribute(): void
    {
        // 创建已停用的属性
        $attribute = $this->service->createAttribute([
            'code' => 'test_attr_to_activate',
            'name' => '待激活属性',
            'status' => AttributeStatus::INACTIVE,
        ]);

        $this->assertEquals(AttributeStatus::INACTIVE, $attribute->getStatus());

        // 激活属性
        $this->service->activateAttribute($attribute);

        $this->assertEquals(AttributeStatus::ACTIVE, $attribute->getStatus());

        // 验证数据库中的状态
        $em = self::getEntityManager();
        $em->clear();

        $foundAttribute = $this->attributeRepository->find($attribute->getId());
        $this->assertNotNull($foundAttribute);
        $this->assertEquals(AttributeStatus::ACTIVE, $foundAttribute->getStatus());
    }

    public function testAddAttributeValueSuccess(): void
    {
        // 先创建属性
        $attribute = $this->service->createAttribute([
            'code' => 'test_attr_with_value',
            'name' => '有值属性',
        ]);

        // 添加属性值
        $valueData = [
            'code' => 'test_attrval_red',
            'value' => '红色',
        ];
        $value = $this->service->addAttributeValue($attribute, $valueData);

        $this->assertNotNull($value->getId());
        $this->assertEquals($attribute->getId(), $value->getAttribute()?->getId());
        $this->assertEquals('test_attrval_red', $value->getCode());
        $this->assertEquals('红色', $value->getValue());

        // 验证数据库持久化
        $em = self::getEntityManager();
        $em->clear();

        $foundValue = $this->attributeValueRepository->find($value->getId());
        $this->assertNotNull($foundValue);
        $this->assertEquals('test_attrval_red', $foundValue->getCode());
    }

    public function testAddAttributeValueWithDuplicateCode(): void
    {
        // 创建属性
        $attribute = $this->service->createAttribute([
            'code' => 'test_attr_duplicate_value',
            'name' => '重复值属性',
        ]);

        // 添加第一个属性值
        $valueData = [
            'code' => 'test_attrval_duplicate',
            'value' => '重复值',
        ];
        $this->service->addAttributeValue($attribute, $valueData);

        // 尝试添加同样 code 的属性值
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage('属性值编码 "test_attrval_duplicate" 已存在');

        $this->service->addAttributeValue($attribute, $valueData);
    }

    public function testImportAttributeValues(): void
    {
        // 创建属性
        $attribute = $this->service->createAttribute([
            'code' => 'test_attr_import',
            'name' => '导入属性',
        ]);

        // 先创建一个已存在的属性值
        $existingValue = $this->service->addAttributeValue($attribute, [
            'code' => 'test_attrval_blue',
            'value' => '蓝色',
        ]);

        // 批量导入属性值
        $valuesData = [
            ['code' => 'test_attrval_red', 'value' => '红色'],
            ['code' => 'test_attrval_blue', 'value' => '蓝色更新'], // 更新已存在的
        ];

        $values = $this->service->importAttributeValues($attribute, $valuesData);

        $this->assertCount(2, $values);

        // 验证新创建的值
        $redValue = array_values(array_filter($values, fn ($v) => $v->getCode() === 'test_attrval_red'))[0] ?? null;
        $this->assertNotNull($redValue);
        $this->assertEquals('红色', $redValue->getValue());

        // 验证更新的值
        $em = self::getEntityManager();
        $em->clear();

        $updatedBlue = $this->attributeValueRepository->find($existingValue->getId());
        $this->assertNotNull($updatedBlue);
        $this->assertEquals('蓝色更新', $updatedBlue->getValue());
    }

    public function testDeactivateAttribute(): void
    {
        // 创建激活的属性
        $attribute = $this->service->createAttribute([
            'code' => 'test_attr_to_deactivate',
            'name' => '待停用属性',
        ]);

        $this->assertEquals(AttributeStatus::ACTIVE, $attribute->getStatus());

        // 停用属性
        $this->service->deactivateAttribute($attribute);

        $this->assertEquals(AttributeStatus::INACTIVE, $attribute->getStatus());

        // 验证数据库中的状态
        $em = self::getEntityManager();
        $em->clear();

        $foundAttribute = $this->attributeRepository->find($attribute->getId());
        $this->assertNotNull($foundAttribute);
        $this->assertEquals(AttributeStatus::INACTIVE, $foundAttribute->getStatus());
    }

    public function testUpdateAttributeValue(): void
    {
        // 创建属性和属性值
        $attribute = $this->service->createAttribute([
            'code' => 'test_attr_update_value',
            'name' => '更新值属性',
        ]);

        $value = $this->service->addAttributeValue($attribute, [
            'code' => 'test_attrval_old',
            'value' => 'Old Value',
        ]);

        // 更新属性值
        $updateData = [
            'code' => 'test_attrval_new',
            'value' => 'New Value',
        ];
        $updatedValue = $this->service->updateAttributeValue($value, $updateData);

        $this->assertEquals('test_attrval_new', $updatedValue->getCode());
        $this->assertEquals('New Value', $updatedValue->getValue());

        // 验证数据库中的更新
        $em = self::getEntityManager();
        $em->clear();

        $foundValue = $this->attributeValueRepository->find($value->getId());
        $this->assertNotNull($foundValue);
        $this->assertEquals('test_attrval_new', $foundValue->getCode());
        $this->assertEquals('New Value', $foundValue->getValue());
    }

    public function testDeleteAttributeValue(): void
    {
        // 创建属性和属性值
        $attribute = $this->service->createAttribute([
            'code' => 'test_attr_delete_value',
            'name' => '删除值属性',
        ]);

        $value = $this->service->addAttributeValue($attribute, [
            'code' => 'test_attrval_to_delete',
            'value' => '待删除值',
        ]);

        $this->assertEquals(AttributeStatus::ACTIVE, $value->getStatus());

        // 删除属性值（软删除）
        $this->service->deleteAttributeValue($value);

        $this->assertEquals(AttributeStatus::INACTIVE, $value->getStatus());

        // 验证数据库中的状态
        $em = self::getEntityManager();
        $em->clear();

        $foundValue = $this->attributeValueRepository->find($value->getId());
        $this->assertNotNull($foundValue);
        $this->assertEquals(AttributeStatus::INACTIVE, $foundValue->getStatus());
    }

    private function cleanupTestData(): void
    {
        try {
            $em = self::getEntityManager();
            if (!$em->isOpen() || !$em->getConnection()->isConnected()) {
                return;
            }

            // 先清理 AttributeValue
            $testValues = $em->createQuery(
                'SELECT v FROM Tourze\ProductCoreBundle\Entity\AttributeValue v WHERE v.code LIKE :pattern'
            )->setParameter('pattern', 'test_attrval_%')->getResult();

            if (is_iterable($testValues)) {
                foreach ($testValues as $value) {
                    $this->assertInstanceOf(AttributeValue::class, $value);
                    $em->remove($value);
                }
            }

            // 再清理 Attribute
            $testAttrs = $em->createQuery(
                'SELECT a FROM Tourze\ProductCoreBundle\Entity\Attribute a WHERE a.code LIKE :pattern'
            )->setParameter('pattern', 'test_attr_%')->getResult();

            if (is_iterable($testAttrs)) {
                foreach ($testAttrs as $attr) {
                    $this->assertInstanceOf(Attribute::class, $attr);
                    $em->remove($attr);
                }
            }

            $em->flush();
        } catch (\Exception $e) {
            // 忽略数据库清理失败的情况
        }
    }
}
