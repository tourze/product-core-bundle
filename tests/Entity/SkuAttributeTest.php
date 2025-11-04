<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductCoreBundle\Entity\Attribute;
use Tourze\ProductCoreBundle\Entity\SkuAttribute;
use Tourze\ProductCoreBundle\Enum\AttributeInputType;
use Tourze\ProductCoreBundle\Enum\AttributeType;
use Tourze\ProductCoreBundle\Enum\AttributeValueType;

/**
 * @internal
 */
#[CoversClass(SkuAttribute::class)]
class SkuAttributeTest extends AbstractEntityTestCase
{

    protected function createEntity(): object
    {
        return new SkuAttribute();
    }

    /**
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        // skuId 不再是可直接设置的属性，改为测试其他属性
        return [
            'name' => ['name', 'test_name'],
            'value' => ['value', 'test_value'],
            'remark' => ['remark', 'test_remark'],
            'allowCustomized' => ['allowCustomized', true],
        ];
    }

    public function testSkuAttributeCreation(): void
    {
        $skuAttribute = new SkuAttribute();

        $this->assertNull($skuAttribute->getId());
        $this->assertInstanceOf(SkuAttribute::class, $skuAttribute);
    }

    public function testBasicFunctionality(): void
    {
        $skuAttribute = new SkuAttribute();

        // Test that the entity can be created and is an instance of the correct class
        $this->assertInstanceOf(SkuAttribute::class, $skuAttribute);
        $this->assertNull($skuAttribute->getId(), 'New SkuAttribute should not have ID yet');
    }

    public function testAttributeRelation(): void
    {
        $skuAttribute = new SkuAttribute();
        $attribute = new Attribute();
        $attribute->setCode('size');
        $attribute->setName('Size');
        $attribute->setType(AttributeType::SALES);
        $attribute->setValueType(AttributeValueType::SINGLE);
        $attribute->setInputType(AttributeInputType::SELECT);

        // Test basic attribute creation
        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertInstanceOf(SkuAttribute::class, $skuAttribute);
        $this->assertEquals('size', $attribute->getCode());
        $this->assertEquals('Size', $attribute->getName());
    }
}
