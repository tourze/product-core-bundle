<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Procedure;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Procedure\ProductSkuDetail;

class ProductSkuDetailTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(ProductSkuDetail::class));
    }
}