<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Integration\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\ProductCoreBundle\Controller\Admin\ProductSpuSubscribeCrudController;

class ProductSpuSubscribeCrudControllerTest extends KernelTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(ProductSpuSubscribeCrudController::class));
    }
}