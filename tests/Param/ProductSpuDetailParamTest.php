<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\ProductCoreBundle\Param\ProductSpuDetailParam;

/**
 * @internal
 */
#[CoversClass(ProductSpuDetailParam::class)]
final class ProductSpuDetailParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new ProductSpuDetailParam(spuId: '123');

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('123', $param->spuId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new ProductSpuDetailParam(spuId: '456');

        $this->assertSame('456', $param->spuId);
    }
}
