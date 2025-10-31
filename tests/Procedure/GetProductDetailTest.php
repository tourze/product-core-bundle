<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\ProductCoreBundle\Exception\ProductNotFoundException;
use Tourze\ProductCoreBundle\Procedure\GetProductDetail;

/**
 * @internal
 */
#[CoversClass(GetProductDetail::class)]
#[RunTestsInSeparateProcesses]
final class GetProductDetailTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testCanBeInstantiated(): void
    {
        $procedure = self::getContainer()->get(GetProductDetail::class);
        $this->assertInstanceOf(GetProductDetail::class, $procedure);
    }

    public function testExecuteWithInvalidId(): void
    {
        $procedure = self::getContainer()->get(GetProductDetail::class);
        $this->assertInstanceOf(GetProductDetail::class, $procedure);
        $procedure->id = 0;

        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('商品ID不能为空');

        $procedure->execute();
    }

    public function testExecuteWithNegativeId(): void
    {
        $procedure = self::getContainer()->get(GetProductDetail::class);
        $this->assertInstanceOf(GetProductDetail::class, $procedure);
        $procedure->id = -1;

        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('商品ID不能为空');

        $procedure->execute();
    }
}
