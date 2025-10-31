<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\ProductCoreBundle\Exception\ProductStatusException;

/**
 * @internal
 */
#[CoversClass(ProductStatusException::class)]
final class ProductStatusExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new ProductStatusException('Invalid status');

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('Invalid status', $exception->getMessage());
    }

    public function testCanBeInstantiatedWithCodeAndPrevious(): void
    {
        $previous = new \RuntimeException('Previous exception');
        $exception = new ProductStatusException('Invalid status', 400, $previous);

        $this->assertSame('Invalid status', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExtendsException(): void
    {
        $exception = new ProductStatusException();

        $this->assertInstanceOf(\Exception::class, $exception);
    }
}
