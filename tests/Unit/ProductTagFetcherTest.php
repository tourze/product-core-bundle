<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\ProductTagFetcher;

class ProductTagFetcherTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(ProductTagFetcher::class));
    }
}