<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductCoreBundle\Entity\Spu;

/**
 * @internal
 */
#[CoversClass(Spu::class)]
final class SpuTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Spu();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'title' => ['title', 'Test Product'],
            'type' => ['type', 'physical'],
            'subtitle' => ['subtitle', 'Test Subtitle'],
            'valid' => ['valid', true],
            'gtin' => ['gtin', '123456789'],
        ];
    }
}
