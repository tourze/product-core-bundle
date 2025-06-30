<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Entity\StockValueAware;

class StockValueAwareTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(trait_exists(StockValueAware::class));
    }
}