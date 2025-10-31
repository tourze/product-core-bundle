<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;
use Tourze\ProductCoreBundle\Event\SpuDetailEvent;

/**
 * @internal
 */
#[CoversClass(SpuDetailEvent::class)]
final class SpuDetailEventTest extends AbstractEventTestCase
{
    public function testCanBeInstantiated(): void
    {
        $event = new SpuDetailEvent();
        $this->assertInstanceOf(SpuDetailEvent::class, $event);
    }
}
