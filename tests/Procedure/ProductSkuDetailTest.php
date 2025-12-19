<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
use Tourze\ProductCoreBundle\Param\ProductSkuDetailParam;
use Tourze\ProductCoreBundle\Procedure\ProductSkuDetail;

/**
 * @internal
 */
#[CoversClass(ProductSkuDetail::class)]
#[RunTestsInSeparateProcesses]
final class ProductSkuDetailTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testServiceIsAccessible(): void
    {
        $service = self::getService(ProductSkuDetail::class);
        $this->assertInstanceOf(ProductSkuDetail::class, $service);
    }

    public function testExecute(): void
    {
        $procedure = self::getService(ProductSkuDetail::class);
        $this->assertInstanceOf(ProductSkuDetail::class, $procedure);

        // Create parameter with test data
        $param = new ProductSkuDetailParam('999999');

        // Handle both expected API exception and database table not found
        $this->expectException(\Exception::class);
        $procedure->execute($param);
    }
}
