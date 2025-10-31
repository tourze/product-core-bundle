<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\ProductCoreBundle\Exception\ProductNotFoundException;

/**
 * @internal
 */
#[CoversClass(ProductNotFoundException::class)]
final class ProductNotFoundExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new ProductNotFoundException('Product not found');

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('Product not found', $exception->getMessage());
    }

    public function testCanBeInstantiatedWithCodeAndPrevious(): void
    {
        $previous = new \RuntimeException('Previous exception');
        $exception = new ProductNotFoundException('Product not found', 404, $previous);

        $this->assertSame('Product not found', $exception->getMessage());
        $this->assertSame(404, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExtendsException(): void
    {
        $exception = new ProductNotFoundException();

        $this->assertInstanceOf(\Exception::class, $exception);
    }
}
