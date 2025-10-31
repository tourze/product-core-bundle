<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductCoreBundle\Entity\Spu;

/**
 * SPU 实体测试
 *
 * @internal
 */
#[CoversClass(Spu::class)]
final class SpuEntityTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的实例
     */
    protected function createEntity(): object
    {
        return new Spu();
    }

    /**
     * 提供属性及其样本值的 Data Provider
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'title' => ['title', 'Test SPU Title'];
        yield 'type' => ['type', 'virtual'];
        yield 'subtitle' => ['subtitle', 'Test SPU Subtitle'];
    }
}
