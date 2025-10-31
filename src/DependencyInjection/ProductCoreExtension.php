<?php

namespace Tourze\ProductCoreBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

final class ProductCoreExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return dirname(__DIR__) . '/Resources/config';
    }
}
