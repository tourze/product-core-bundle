<?php

declare(strict_types=1);

namespace Tourze\ProductCoreBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class ProductSkuDetailParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: 'SKU ID')]
        public string $skuId,
    ) {
    }
}
