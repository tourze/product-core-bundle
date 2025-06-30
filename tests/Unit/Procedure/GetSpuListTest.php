<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Procedure;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Procedure\GetSpuList;

class GetSpuListTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(GetSpuList::class));
    }
}