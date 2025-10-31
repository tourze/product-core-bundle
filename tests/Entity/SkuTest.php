<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductCoreBundle\Entity\Sku;

/**
 * @internal
 */
#[CoversClass(Sku::class)]
final class SkuTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的实例
     */
    protected function createEntity(): object
    {
        return new Sku();
    }

    /**
     * 提供属性及其样本值的 Data Provider
     *
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'unit' => ['unit', 'pcs'];
        yield 'gtin' => ['gtin', 'SKU123456'];
    }
}
