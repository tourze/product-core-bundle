<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Integration\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\ProductCoreBundle\Command\CibMallCrawlSpuCommand;

class CibMallCrawlSpuCommandTest extends KernelTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(CibMallCrawlSpuCommand::class));
    }
}