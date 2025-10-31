<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\ProductCoreBundle\Exception\AdvertisementLimitException;

/**
 * @internal
 */
#[CoversClass(AdvertisementLimitException::class)]
final class AdvertisementLimitExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new AdvertisementLimitException('Test message');
        $this->assertInstanceOf(AdvertisementLimitException::class, $exception);
        $this->assertSame('Test message', $exception->getMessage());
    }
}
