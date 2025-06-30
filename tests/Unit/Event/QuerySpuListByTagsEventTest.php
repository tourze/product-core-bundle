<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use Tourze\ProductCoreBundle\Event\QuerySpuListByTagsEvent;

class QuerySpuListByTagsEventTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $this->assertTrue(class_exists(QuerySpuListByTagsEvent::class));
    }
}