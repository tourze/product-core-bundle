<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Exception\StockOverloadException;

class StockOverloadExceptionTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(StockOverloadException::class));
    }
}