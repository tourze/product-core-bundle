<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Integration\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\ProductCoreBundle\Controller\Admin\ProductSkuAttributeCrudController;

class ProductSkuAttributeCrudControllerTest extends KernelTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(ProductSkuAttributeCrudController::class));
    }
}