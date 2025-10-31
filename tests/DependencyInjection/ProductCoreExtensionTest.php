<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\ProductCoreBundle\DependencyInjection\ProductCoreExtension;

/**
 * @internal
 */
#[CoversClass(ProductCoreExtension::class)]
final class ProductCoreExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $extension = new ProductCoreExtension();
        $this->assertInstanceOf(ProductCoreExtension::class, $extension);
    }

    public function testLoad(): void
    {
        $extension = new ProductCoreExtension();
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $extension->load([], $container);

        $this->assertInstanceOf(ContainerBuilder::class, $container);
    }
}
