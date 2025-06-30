<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Integration\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\ProductCoreBundle\Command\AutoReleaseSpuCommand;

class AutoReleaseSpuCommandTest extends KernelTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(AutoReleaseSpuCommand::class));
    }
}