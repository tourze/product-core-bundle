<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\ProductCoreBundle\Controller\TempController;

class TempControllerTest extends KernelTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(TempController::class));
    }
}