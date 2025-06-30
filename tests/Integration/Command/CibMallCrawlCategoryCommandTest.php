<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Integration\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\ProductCoreBundle\Command\CibMallCrawlCategoryCommand;

class CibMallCrawlCategoryCommandTest extends KernelTestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(CibMallCrawlCategoryCommand::class));
    }
}