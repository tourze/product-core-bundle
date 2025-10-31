<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;
use Tourze\ProductCoreBundle\Event\QuerySpuListByAttributesEvent;

/**
 * @internal
 */
#[CoversClass(QuerySpuListByAttributesEvent::class)]
final class QuerySpuListByAttributesEventTest extends AbstractEventTestCase
{
    public function testCanBeInstantiated(): void
    {
        $event = new QuerySpuListByAttributesEvent();
        $this->assertInstanceOf(QuerySpuListByAttributesEvent::class, $event);
    }
}
