<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Event\QuerySpuListByAttributesEvent;

class QuerySpuListByAttributesEventTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(QuerySpuListByAttributesEvent::class));
    }
}