<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Spu;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Spu\Material;

/**
 * @internal
 */
#[CoversClass(Material::class)]
final class MaterialTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $material = new Material();
        $this->assertInstanceOf(Material::class, $material);
    }
}
