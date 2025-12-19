<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\ProductCoreBundle\Param\GetProductDetailParam;

/**
 * @internal
 */
#[CoversClass(GetProductDetailParam::class)]
final class GetProductDetailParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetProductDetailParam(id: 123);

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame(123, $param->id);
    }

    public function testParamWithDefaultValue(): void
    {
        $param = new GetProductDetailParam();

        $this->assertSame(0, $param->id);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetProductDetailParam(id: 456);

        $this->assertSame(456, $param->id);
    }
}
