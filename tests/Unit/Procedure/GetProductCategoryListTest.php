<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Procedure;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Procedure\GetProductCategoryList;

class GetProductCategoryListTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(GetProductCategoryList::class));
    }
}