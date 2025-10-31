<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\ProductCoreBundle\Exception\DataProcessingException;

/**
 * @internal
 */
#[CoversClass(DataProcessingException::class)]
final class DataProcessingExceptionTest extends AbstractExceptionTestCase
{
    public function testInstantiation(): void
    {
        $exception = new DataProcessingException('Test message');

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testWithCodeAndPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new DataProcessingException('Test message', 123, $previous);

        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(123, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
