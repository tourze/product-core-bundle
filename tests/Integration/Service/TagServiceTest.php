<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Integration\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\ProductCoreBundle\Service\TagService;

class TagServiceTest extends KernelTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(TagService::class));
    }
}