<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\ProductCoreBundle\Param\ProductSkuDetailParam;

/**
 * @internal
 */
#[CoversClass(ProductSkuDetailParam::class)]
final class ProductSkuDetailParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new ProductSkuDetailParam(skuId: '123');

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('123', $param->skuId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new ProductSkuDetailParam(skuId: '456');

        $this->assertSame('456', $param->skuId);
    }
}
