<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Integration\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\ProductCoreBundle\Repository\SpuSubscribeRepository;

class SpuSubscribeRepositoryTest extends KernelTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(SpuSubscribeRepository::class));
    }
}