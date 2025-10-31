<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;
use Tourze\ProductCoreBundle\Event\QuerySpuListByTagsEvent;

/**
 * @internal
 */
#[CoversClass(QuerySpuListByTagsEvent::class)]
final class QuerySpuListByTagsEventTest extends AbstractEventTestCase
{
    public function testCanBeInstantiated(): void
    {
        $event = new QuerySpuListByTagsEvent();
        $this->assertInstanceOf(QuerySpuListByTagsEvent::class, $event);
    }
}
