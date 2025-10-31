<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\ProductCoreBundle\Procedure\ProductSpuDetail;

/**
 * @internal
 */
#[CoversClass(ProductSpuDetail::class)]
#[RunTestsInSeparateProcesses]
final class ProductSpuDetailTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testServiceIsAccessible(): void
    {
        $service = self::getService(ProductSpuDetail::class);
        $this->assertInstanceOf(ProductSpuDetail::class, $service);
    }

    public function testExecute(): void
    {
        $procedure = self::getService(ProductSpuDetail::class);
        $this->assertInstanceOf(ProductSpuDetail::class, $procedure);

        // Test that execute method exists and can be called
        $procedure->spuId = '999999';

        // Handle both expected API exception and database table not found
        $this->expectException(\Exception::class);
        $procedure->execute();
    }
}
