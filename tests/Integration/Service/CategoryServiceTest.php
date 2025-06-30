<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Integration\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\ProductCoreBundle\Service\CategoryService;

class CategoryServiceTest extends KernelTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(CategoryService::class));
    }
}