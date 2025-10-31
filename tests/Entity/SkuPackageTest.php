<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductCoreBundle\Entity\SkuPackage;
use Tourze\ProductCoreBundle\Enum\PackageType;

/**
 * @internal
 */
#[CoversClass(SkuPackage::class)]
final class SkuPackageTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的实例
     */
    protected function createEntity(): object
    {
        return new SkuPackage();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'type' => ['type', PackageType::COUPON],
            'value' => ['value', '折扣券'],
            'quantity1' => ['quantity', 1],
            'quantity2' => ['quantity', 5],
            'remark' => ['remark', '这是一个优惠券'],
        ];
    }
}
