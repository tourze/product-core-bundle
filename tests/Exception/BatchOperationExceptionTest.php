<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\ProductCoreBundle\Exception\BatchOperationException;

/**
 * @internal
 */
#[CoversClass(BatchOperationException::class)]
final class BatchOperationExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new BatchOperationException('Test message');

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('Test message', $exception->getMessage());
    }

    public function testCanBeInstantiatedWithCodeAndPrevious(): void
    {
        $previous = new \RuntimeException('Previous exception');
        $exception = new BatchOperationException('Test message', 123, $previous);

        $this->assertSame('Test message', $exception->getMessage());
        $this->assertSame(123, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExtendsException(): void
    {
        $exception = new BatchOperationException();

        $this->assertInstanceOf(\Exception::class, $exception);
    }
}
