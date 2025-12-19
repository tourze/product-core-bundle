<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetProductDetailParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '商品ID')]
        public int $id = 0,
    ) {
    }
}
