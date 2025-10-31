<?php

namespace Tourze\ProductCoreBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\ProductCoreBundle\Exception\ParameterValidationException;

/**
 * @internal
 */
#[CoversClass(ParameterValidationException::class)]
final class ParameterValidationExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new ParameterValidationException('Test message');
        $this->assertInstanceOf(ParameterValidationException::class, $exception);
        $this->assertSame('Test message', $exception->getMessage());
    }

    public function testCanBeInstantiatedWithCodeAndPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new ParameterValidationException('Test message', 100, $previous);

        $this->assertSame('Test message', $exception->getMessage());
        $this->assertSame(100, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExtendsException(): void
    {
        $exception = new ParameterValidationException('Test message');
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
    }
}
