<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\ProductCoreBundle\Entity\Brand;

/**
 * @internal
 */
#[CoversClass(Brand::class)]
final class BrandTest extends AbstractEntityTestCase
{
    protected function createEntity(): Brand
    {
        return new Brand();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name' => ['name', 'Test Brand'],
            'valid1' => ['valid', true],
            'valid2' => ['valid', false],
            'logoUrl' => ['logoUrl', 'https://example.com/logo.png'],
        ];
    }

    public function testCanBeInstantiated(): void
    {
        $brand = new Brand();
        $this->assertInstanceOf(Brand::class, $brand);
    }

    public function testSetAndGetName(): void
    {
        $brand = new Brand();
        $name = 'Test Brand';

        $brand->setName($name);
        $this->assertSame($name, $brand->getName());
    }

    public function testSetAndGetValid(): void
    {
        $brand = new Brand();

        $brand->setValid(true);
        $this->assertTrue($brand->isValid());

        $brand->setValid(false);
        $this->assertFalse($brand->isValid());
    }

    public function testSetAndGetLogoUrl(): void
    {
        $brand = new Brand();
        $logoUrl = 'https://example.com/logo.png';

        $brand->setLogoUrl($logoUrl);
        $this->assertSame($logoUrl, $brand->getLogoUrl());
    }
}
