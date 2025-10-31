<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\DTO\ProductAttribute;

/**
 * @internal
 */
#[CoversClass(ProductAttribute::class)]
final class ProductAttributeTest extends TestCase
{
    public function testConstruct(): void
    {
        $attribute = new ProductAttribute(1, 'Color', 'Red');

        $this->assertSame(1, $attribute->id);
        $this->assertSame('Color', $attribute->name);
        $this->assertSame('Red', $attribute->value);
        $this->assertNull($attribute->type);
    }

    public function testConstructWithType(): void
    {
        $attribute = new ProductAttribute(2, 'Size', 'XL', 'string');

        $this->assertSame(2, $attribute->id);
        $this->assertSame('Size', $attribute->name);
        $this->assertSame('XL', $attribute->value);
        $this->assertSame('string', $attribute->type);
    }

    public function testToArray(): void
    {
        $attribute = new ProductAttribute(3, 'Weight', '1.5kg', 'text');
        $array = $attribute->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('type', $array);

        $this->assertSame(3, $array['id']);
        $this->assertSame('Weight', $array['name']);
        $this->assertSame('1.5kg', $array['value']);
        $this->assertSame('text', $array['type']);
        $this->assertCount(4, $array);
    }

    public function testToArrayWithNullType(): void
    {
        $attribute = new ProductAttribute(4, 'Brand', 'Nike');
        $array = $attribute->toArray();

        $this->assertSame(4, $array['id']);
        $this->assertSame('Brand', $array['name']);
        $this->assertSame('Nike', $array['value']);
        $this->assertNull($array['type']);
    }
}
