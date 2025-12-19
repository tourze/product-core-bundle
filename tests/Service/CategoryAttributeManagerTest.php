<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CatalogBundle\Entity\Catalog;
use Tourze\CatalogBundle\Entity\CatalogType;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ProductCoreBundle\Entity\Attribute;
use Tourze\ProductCoreBundle\Entity\AttributeGroup;
use Tourze\ProductCoreBundle\Entity\CategoryAttribute;
use Tourze\ProductCoreBundle\Service\CategoryAttributeManager;

/**
 * @internal
 */
#[CoversClass(CategoryAttributeManager::class)]
#[RunTestsInSeparateProcesses]
final class CategoryAttributeManagerTest extends AbstractIntegrationTestCase
{
    private CategoryAttributeManager $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(CategoryAttributeManager::class);
        $this->cleanupTestData();
    }

    protected function onTearDown(): void
    {
        $this->cleanupTestData();
    }

    public function testAssociateAttributeCreatesNewAssociation(): void
    {
        $catalog = $this->createTestCatalog('TEST-CATALOG-NEW-ASSOC');
        $attribute = $this->createTestAttribute('TEST-ATTR-NEW-ASSOC');
        $group = $this->createTestAttributeGroup('TEST-ATTRGROUP-NEW-ASSOC');

        $result = $this->service->associateAttribute(
            $catalog,
            $attribute,
            $group,
            ['isRequired' => true, 'sortOrder' => 10]
        );

        $this->assertInstanceOf(CategoryAttribute::class, $result);
        $this->assertSame($catalog, $result->getCategory());
        $this->assertSame($attribute, $result->getAttribute());
        $this->assertSame($group, $result->getGroup());
        $this->assertTrue($result->getIsRequired());
        $this->assertSame(10, $result->getSortOrder());
    }

    public function testAssociateAttributeUpdatesExistingAssociation(): void
    {
        $catalog = $this->createTestCatalog('TEST-CATALOG-UPDATE-ASSOC');
        $attribute = $this->createTestAttribute('TEST-ATTR-UPDATE-ASSOC');
        $group1 = $this->createTestAttributeGroup('TEST-ATTRGROUP-UPDATE-ASSOC-1');
        $group2 = $this->createTestAttributeGroup('TEST-ATTRGROUP-UPDATE-ASSOC-2');

        // 首次关联
        $first = $this->service->associateAttribute(
            $catalog,
            $attribute,
            $group1,
            ['isRequired' => false]
        );
        $firstId = $first->getId();

        // 再次关联，应该更新现有记录
        $result = $this->service->associateAttribute(
            $catalog,
            $attribute,
            $group2,
            ['isRequired' => true]
        );

        $this->assertSame($firstId, $result->getId());
        $this->assertSame($group2, $result->getGroup());
        $this->assertTrue($result->getIsRequired());
    }

    public function testBatchAssociate(): void
    {
        $catalog = $this->createTestCatalog('TEST-CATALOG-BATCH');
        $attribute1 = $this->createTestAttribute('TEST-ATTR-BATCH-1');
        $attribute2 = $this->createTestAttribute('TEST-ATTR-BATCH-2');
        $group = $this->createTestAttributeGroup('TEST-ATTRGROUP-BATCH');

        $attributesData = [
            [
                'attribute' => $attribute1,
                'group' => $group,
                'isRequired' => true,
                'sortOrder' => 5,
            ],
            [
                'attribute' => $attribute2,
                'isVisible' => false,
            ],
        ];

        $result = $this->service->batchAssociate($catalog, $attributesData);

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(CategoryAttribute::class, $result);
        $this->assertSame($attribute1, $result[0]->getAttribute());
        $this->assertTrue($result[0]->getIsRequired());
        $this->assertSame(5, $result[0]->getSortOrder());
        $this->assertSame($attribute2, $result[1]->getAttribute());
        $this->assertFalse($result[1]->isVisible());
    }

    public function testUpdateCategoryAttribute(): void
    {
        $catalog = $this->createTestCatalog('TEST-CATALOG-UPDATE');
        $attribute = $this->createTestAttribute('TEST-ATTR-UPDATE');

        $categoryAttribute = $this->service->associateAttribute(
            $catalog,
            $attribute,
            null,
            ['isRequired' => true, 'sortOrder' => 10]
        );

        $data = ['isRequired' => false, 'sortOrder' => 20];

        $result = $this->service->updateCategoryAttribute($categoryAttribute, $data);

        $this->assertSame($categoryAttribute, $result);
        $this->assertFalse($result->getIsRequired());
        $this->assertSame(20, $result->getSortOrder());
    }

    public function testDissociateAttributeWithExistingAssociation(): void
    {
        $catalog = $this->createTestCatalog('TEST-CATALOG-DISSOC');
        $attribute = $this->createTestAttribute('TEST-ATTR-DISSOC');

        $this->service->associateAttribute($catalog, $attribute);

        $em = self::getEntityManager();
        $em->clear();

        // 验证关联存在
        $repository = $em->getRepository(CategoryAttribute::class);
        $before = $repository->findOneBy([
            'category' => $catalog,
            'attribute' => $attribute,
        ]);
        $this->assertInstanceOf(CategoryAttribute::class, $before);

        // 移除关联
        $this->service->dissociateAttribute($catalog, $attribute);

        $em->clear();

        // 验证关联已被删除
        $after = $repository->findOneBy([
            'category' => $catalog,
            'attribute' => $attribute,
        ]);
        $this->assertNull($after);
    }

    public function testDissociateAttributeWithNoExistingAssociation(): void
    {
        $catalog = $this->createTestCatalog('TEST-CATALOG-DISSOC-NONE');
        $attribute = $this->createTestAttribute('TEST-ATTR-DISSOC-NONE');

        // 移除不存在的关联，应该不报错
        $this->service->dissociateAttribute($catalog, $attribute);

        // 验证没有异常抛出
        $this->assertTrue(true);
    }

    public function testAssociateAttributeWithMinimalOptions(): void
    {
        $catalog = $this->createTestCatalog('TEST-CATALOG-MINIMAL');
        $attribute = $this->createTestAttribute('TEST-ATTR-MINIMAL');

        $result = $this->service->associateAttribute($catalog, $attribute);

        $this->assertInstanceOf(CategoryAttribute::class, $result);
        $this->assertSame($catalog, $result->getCategory());
        $this->assertSame($attribute, $result->getAttribute());
        $this->assertNull($result->getGroup());
    }

    public function testCopyAttributesToCategory(): void
    {
        $sourceCategory = $this->createTestCatalog('TEST-CATALOG-COPY-SOURCE');
        $targetCategory = $this->createTestCatalog('TEST-CATALOG-COPY-TARGET');
        $attribute = $this->createTestAttribute('TEST-ATTR-COPY');
        $group = $this->createTestAttributeGroup('TEST-ATTRGROUP-COPY');

        // 为源类目添加属性
        $this->service->associateAttribute(
            $sourceCategory,
            $attribute,
            $group,
            [
                'isRequired' => true,
                'isVisible' => true,
                'defaultValue' => 'default',
                'allowedValues' => ['value1', 'value2'],
                'sortOrder' => 10,
                'config' => ['key' => 'value'],
            ]
        );

        // 复制到目标类目
        $result = $this->service->copyAttributesToCategory($sourceCategory, $targetCategory);

        $this->assertCount(1, $result);
        $this->assertContainsOnlyInstancesOf(CategoryAttribute::class, $result);

        $copied = $result[0];
        $this->assertSame($targetCategory, $copied->getCategory());
        $this->assertSame($attribute, $copied->getAttribute());
        $this->assertSame($group, $copied->getGroup());
        $this->assertTrue($copied->getIsRequired());
        $this->assertTrue($copied->isVisible());
        $this->assertSame('default', $copied->getDefaultValue());
        $this->assertSame(['value1', 'value2'], $copied->getAllowedValues());
        $this->assertSame(10, $copied->getSortOrder());
        $this->assertSame(['key' => 'value'], $copied->getConfig());
    }

    public function testCopyAttributesToCategoryWithEmptySource(): void
    {
        $sourceCategory = $this->createTestCatalog('TEST-CATALOG-COPY-EMPTY-SOURCE');
        $targetCategory = $this->createTestCatalog('TEST-CATALOG-COPY-EMPTY-TARGET');

        // 源类目没有属性
        $result = $this->service->copyAttributesToCategory($sourceCategory, $targetCategory);

        $this->assertCount(0, $result);
    }

    public function testDissociateAllAttributes(): void
    {
        $catalog = $this->createTestCatalog('TEST-CATALOG-DISSOC-ALL');
        $attribute1 = $this->createTestAttribute('TEST-ATTR-DISSOC-ALL-1');
        $attribute2 = $this->createTestAttribute('TEST-ATTR-DISSOC-ALL-2');

        // 添加多个属性
        $this->service->associateAttribute($catalog, $attribute1);
        $this->service->associateAttribute($catalog, $attribute2);

        $em = self::getEntityManager();
        $em->clear();

        // 验证关联存在
        $repository = $em->getRepository(CategoryAttribute::class);
        $before = $repository->findBy(['category' => $catalog]);
        $this->assertCount(2, $before);

        // 移除所有关联
        $this->service->dissociateAllAttributes($catalog);

        $em->clear();

        // 验证所有关联已被删除
        $after = $repository->findBy(['category' => $catalog]);
        $this->assertCount(0, $after);
    }

    public function testBasicOperations(): void
    {
        $service = self::getService(CategoryAttributeManager::class);
        $this->assertInstanceOf(CategoryAttributeManager::class, $service);
    }

    private function createTestCatalog(string $name): Catalog
    {
        $catalogType = new CatalogType();
        $catalogType->setCode('test_catalog_type_' . uniqid());
        $catalogType->setName('Test Catalog Type');

        $catalog = new Catalog();
        $catalog->setType($catalogType);
        $catalog->setName($name);

        $em = self::getEntityManager();
        $em->persist($catalogType);
        $em->persist($catalog);
        $em->flush();

        return $catalog;
    }

    private function createTestAttribute(string $code): Attribute
    {
        $attr = new Attribute();
        $attr->setCode($code);
        $attr->setName('Test Attribute ' . $code);

        $em = self::getEntityManager();
        $em->persist($attr);
        $em->flush();

        return $attr;
    }

    private function createTestAttributeGroup(string $code): AttributeGroup
    {
        $group = new AttributeGroup();
        $group->setCode($code);
        $group->setName('Test Group ' . $code);

        $em = self::getEntityManager();
        $em->persist($group);
        $em->flush();

        return $group;
    }

    private function cleanupTestData(): void
    {
        try {
            $em = self::getEntityManager();
            if (!$em->isOpen() || !$em->getConnection()->isConnected()) {
                return;
            }

            // 清理顺序很重要：先清理关联表，再清理主表
            $connection = $em->getConnection();

            // 1. CategoryAttribute (关联表)
            $connection->executeStatement(
                "DELETE ca FROM product_category_attribute ca
                 INNER JOIN product_attribute a ON ca.attribute_id = a.id
                 WHERE a.code LIKE 'TEST-ATTR-%'"
            );

            // 2. AttributeGroup
            $connection->executeStatement(
                "DELETE FROM product_attribute_group WHERE code LIKE 'TEST-ATTRGROUP-%'"
            );

            // 3. Attribute
            $connection->executeStatement(
                "DELETE FROM product_attribute WHERE code LIKE 'TEST-ATTR-%'"
            );

            // 4. Catalog
            $connection->executeStatement(
                "DELETE FROM catalogs WHERE name LIKE 'TEST-CATALOG-%'"
            );

            // 5. CatalogType (清理所有测试创建的临时类型)
            $connection->executeStatement(
                "DELETE FROM catalog_types WHERE code LIKE 'test_catalog_type_%'"
            );

            $em->clear();
        } catch (\Exception $e) {
            // ignore cleanup errors
        }
    }
}
